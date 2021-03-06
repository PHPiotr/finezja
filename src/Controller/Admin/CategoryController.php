<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\File\Exception\IniSizeFileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/oferta", name="adminCategories")
     */
    public function renderListCategoriesView()
    {
        return $this->render('admin/categories/list.html.twig');
    }

    /**
     * @Route("/admin/oferta/nowa-kategoria", name="newCategory")
     */
    public function renderNewCategoryView()
    {
        return $this->render('admin/categories/new.html.twig', []);
    }

    /**
     * @Route("/admin/oferta/{id}", name="adminCategory")
     * @param Category $category
     * @return Response
     */
    public function renderEditCategoryView(Category $category)
    {
        return $this->render('admin/categories/edit.html.twig', [
            'categoryId' => $category->getId(),
        ]);
    }

    /**
     * @Route("/admin/categories/{id}", methods={"GET"}, name="fetchCategory")
     * @param Category $category
     * @return JsonResponse
     */
    public function fetchCategoryAction(Category $category)
    {
        return $this->json([
            'category' => $this->getParsedCategoryForResponse($category, $this->getDoctrine()->getRepository(Image::class)),
        ]);
    }


    /**
     * @Route("/admin/categories", methods={"GET"}, name="fetchCategories")
     * @return JsonResponse
     */
    public function fetchCategoriesAction()
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findBy([], ['sort' => 'ASC']);
        $filesystem = new Filesystem();
        $publicDir = $this->getParameter('public_directory');

        $data = [];
        foreach ($categories as $category) {
            $slide = "/images/slider/slide-{$category->getId()}.jpg";
            $data[] = [
                'id' => $category->getId(),
                'slug' => $category->getSlug(),
                'name' => $category->getName(),
                'sort' => $category->getSort(),
                'image' => $category->getImage(),
                'slide' => $filesystem->exists("{$publicDir}/{$slide}") ? $slide : null,
            ];
        }

        return $this->json([
            'categories' => $data,
        ]);
    }

    /**
     * @Route("/admin/categories/{id}/slider", methods={"POST"}, name="addCategorySlide")
     * @param Category $category
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sliderAction(Category $category, Request $request)
    {
        $uploadedFiles = $request->files->all();
        if (!isset($uploadedFiles['slide'])) {
            throw new Exception('Missing slide');
        }
        $uploadedFile = $uploadedFiles['slide'];
        $clientOriginalName = $uploadedFile->getClientOriginalName();
        $originalExtension = pathinfo($clientOriginalName, PATHINFO_EXTENSION);
        $newFilename = sprintf('slide-%d.%s', $category->getId(), $originalExtension);
        $uploadDir = $this->getParameter('images_directory') . '/slider';
        $uploadedFile->move($uploadDir, $newFilename);

        return $this->json([
            'slide' => "/images/slider/{$newFilename}",
        ]);
    }

    /**
     * @Route("/admin/categories/add", methods={"POST"}, name="addCategory")
     * @param ValidatorInterface $validator
     * @param Request $request
     * @return JsonResponse
     */
    public function addCategoryAction(ValidatorInterface $validator, Request $request)
    {
        $slugify = new Slugify();

        $category = new Category();
        $category->setName($request->request->get('name'));
        $category->setSlug($slugify->slugify($category->getName()));
        $category->setShortDescription($request->request->get('shortDescription'));
        $category->setLongDescription($request->request->get('longDescription'));

        $categoryImage = $request->request->get('image');
        $slug = $category->getSlug();
        $uploadedFiles = $request->files->all();
        $failedUploads = [];
        $imagesSort = 1;
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categorySort = $repo->getMaxSort();
        $category->setSort($categorySort + 1);
        $entityManager = $this->getDoctrine()->getManager();
        $mainCategoryImageName = '';
        $succeeded = [];
        $uploadDir = $this->getParameter('images_directory') . '/offer';
        $imageDescriptions = $request->request->get('imageDescriptions') ?? [];
        $index = 0;
        foreach ($uploadedFiles as $uploadedFile) {
            $clientOriginalName = $uploadedFile->getClientOriginalName();
            $originalFilename = pathinfo($clientOriginalName, PATHINFO_FILENAME);
            $originalExtension = pathinfo($clientOriginalName, PATHINFO_EXTENSION);
            $newFilename = sprintf('%s-%s.%s', $slug, uniqid(), $originalExtension);
            if (strpos($categoryImage, $originalFilename) !== false) {
                $category->setImage("/images/offer/{$newFilename}");
                $mainCategoryImageName = $clientOriginalName;
            }
            try {
                $uploadedFile->move($uploadDir, $newFilename);
                $succeeded[] = $newFilename;
            } catch (FileException $e) {
                $failedUploads[$clientOriginalName] = [$e->getMessage()];
            } catch (IniSizeFileException $e) {
                $failedUploads[$clientOriginalName] = [$e->getMessage()];
            }
            if (!isset($failedUploads[$clientOriginalName])) {
                $image = new Image();
                $image->setName("/images/offer/{$newFilename}");
                $image->setDescription(empty($imageDescriptions[$index]) ? null : $imageDescriptions[$index]);
                $image->setSort($imagesSort++);
                $index++;
                $errors = $validator->validate($image);
                if (count($errors) > 0) {
                    $failedUploads[$clientOriginalName] = $errors;
                    continue;
                }

                $entityManager->persist($image);
                $category->addImage($image);
            }
        }

        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 400);
        }
        if (isset($failedUploads[$mainCategoryImageName])) {
            $category->setImage(sprintf('%s', isset($succeeded[0]) ? '/images/offer/'.$succeeded[0] : ''));
        }
        $entityManager->persist($category);
        $entityManager->flush();

        $imageRepo = $this->getDoctrine()->getRepository(Image::class);
        return $this->json([
            'succeededUploads' => $succeeded,
            'failedUploads' => $failedUploads,
            'category' => $this->getParsedCategoryForResponse($category, $imageRepo),
        ]);
    }

    /**
     * @Route("/admin/categories/{id}", methods={"POST"}, name="editCategory")
     * @param Category $category
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function editCategoryAction(Category $category, Request $request, ValidatorInterface $validator)
    {
        $slugify = new Slugify();
        $categoryImage = $request->request->get('image');

        $category->setName($request->request->get('name'));
        $category->setSlug($slugify->slugify($category->getName()));
        $category->setShortDescription($request->request->get('shortDescription'));
        $category->setLongDescription($request->request->get('longDescription'));
        $category->setImage($request->request->get('image'));

        $slug = $category->getSlug();
        $uploadedFiles = $request->files->all();
        $failedUploads = [];
        $imagesSort = 1;
        $entityManager = $this->getDoctrine()->getManager();
        $mainCategoryImageName = '';
        $succeeded = [];
        $fileNames = $request->request->get('fileNames');
        $imageDescriptions = $request->request->get('imageDescriptions') ?? [];
        $fileRepo = $this->getDoctrine()->getRepository(Image::class);
        $uploadedImagesDescriptions = [];
        foreach ($fileNames as $key => $fileName) {
            $existingImage = $fileRepo->findOneBy(['name' => $fileName, 'Category' => $category->getId()]);
            if (!$existingImage) {
                $uploadedImagesDescriptions[$fileName] = $imageDescriptions[$key] ?? null;
                continue;
            }
            $existingImage->setSort($key + 1);
            $existingImage->setDescription(empty($imageDescriptions[$key]) ? null : $imageDescriptions[$key]);
            $entityManager->persist($existingImage);
        }

        foreach ($uploadedFiles as $uploadedFile) {
            $clientOriginalName = $uploadedFile->getClientOriginalName();
            $originalFilename = pathinfo($clientOriginalName, PATHINFO_FILENAME);
            $originalExtension = pathinfo($clientOriginalName, PATHINFO_EXTENSION);
            $newFilename = sprintf('%s-%s.%s', $slug, uniqid(), $originalExtension);
            if (strpos($categoryImage, $originalFilename) !== false) {
                $category->setImage("/images/offer/{$newFilename}");
                $mainCategoryImageName = $clientOriginalName;
            }
            try {
                $uploadedFile->move(
                    $this->getParameter('images_directory') . '/offer',
                    $newFilename
                );
                $succeeded[] = $newFilename;
            } catch (FileException $e) {
                $failedUploads[$clientOriginalName] = $e->getMessage();
            } catch (IniSizeFileException $e) {
                $failedUploads[$clientOriginalName] = $e->getMessage();
            }
            if (!isset($failedUploads[$clientOriginalName])) {
                $image = new Image();
                $image->setName("/images/offer/{$newFilename}");
                $image->setDescription(empty($uploadedImagesDescriptions[$clientOriginalName]) ? null : $uploadedImagesDescriptions[$clientOriginalName]);
                $fileNameIndex = array_search($clientOriginalName, $fileNames);
                if ($fileNameIndex === false) {
                    $image->setSort($imagesSort);
                } else {
                    $image->setSort($fileNameIndex + 1);
                }
                $imagesSort++;
                $errors = $validator->validate($image);
                if (count($errors) > 0) {
                    return $this->json(['errors' => $errors], 400);
                }

                $entityManager->persist($image);
                $category->addImage($image);
            }
        }

        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 400);
        }

        if (isset($failedUploads[$mainCategoryImageName])) {
            $category->setImage(sprintf('%s', isset($succeeded[0]) ? '/images/offer/'.$succeeded[0] : ''));
        }

        $filesystem = new Filesystem();
        $imagesToRemove = $request->request->get('imagesToRemove') ?? [];
        $imageRepo = $this->getDoctrine()->getRepository(Image::class);
        $publicDir = $this->getParameter('public_directory');
        foreach ($imagesToRemove as $name) {
            $image = $imageRepo->findOneByName($name);
            if (!$image) {
                continue;
            }
            $category->removeImage($image);
            $pathToImage = $publicDir . $image->getName();
            if ($filesystem->exists($pathToImage)) {
                $filesystem->remove([$pathToImage]);
            }
        }

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'succeededUploads' => $succeeded,
            'failedUploads' => $failedUploads,
            'category' => $this->getParsedCategoryForResponse($category, $imageRepo),
        ]);
    }

    /**
     * @Route("/admin/categories/sort", methods={"PUT"}, name="adminSortCategories")
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    public function sortCategoriesAction(Request $request)
    {
        $jsonData = $request->getContent();
        $decodedData = json_decode($jsonData);
        $jsonLastError = json_last_error();
        if ($jsonLastError !== JSON_ERROR_NONE) {
            throw new JsonException("Unable to decode json: {$jsonLastError}");
        }
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $slug = $decodedData->slug;
        $category = $repo->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw new BadRequestHttpException("No category found for slug: {$slug}");
        }
        $oldSort = $category->getSort();
        $oldIndex = $decodedData->oldIndex;
        $newIndex = $decodedData->newIndex;
        $newSort = $oldSort + $newIndex - $oldIndex;
        $repo->sortCategories($category, $oldSort, $newSort < 0 ? 0 : $newSort);

        return $this->json([
            'name' => $category->getName(),
            'slug' => $slug,
            'oldSort' => $oldSort,
            'newSort' => $newSort,
        ]);
    }

    /**
     * @Route("/admin/categories/{id}", methods={"DELETE"}, name="adminDeleteCategory")
     * @param Category $category
     * @return Response
     * @throws Exception
     */
    public function deleteCategoryAction(Category $category)
    {
        $conn = $this->getDoctrine()->getConnection();
        $entityManager = $this->getDoctrine()->getManager();
        $publicDir = $this->getParameter('public_directory');
        $filesystem = new Filesystem();
        try {
            $conn->beginTransaction();
            foreach ($category->getImages()->toArray() as $image) {
                $category->removeImage($image);
                $entityManager->remove($image);
                $imagePath = $publicDir . $image->getName();
                try {
                    if ($filesystem->exists($imagePath)) {
                        $filesystem->remove($imagePath);
                    }
                } catch (IOExceptionInterface $e) {
                    // TODO: Log failed deletions.
                }
            }
            $entityManager->remove($category);
            $entityManager->flush();
            $conn->commit();
            $response = new Response();
            $response->setStatusCode(204);
            return $response;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    private function getParsedCategoryForResponse($category, $imageRepo): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'shortDescription' => $category->getShortDescription(),
            'longDescription' => $category->getLongDescription(),
            'slug' => $category->getSlug(),
            'sort' => $category->getSort(),
            'image' => $category->getImage(),
            'images' => array_values(array_map(function($image) {
                return [
                    'name' => $image->getName(),
                    'description' => $image->getDescription(),
                    'sort' => $image->getSort(),
                ];
            }, $imageRepo->getAllSortedFromCategory($category))),
        ];
    }
}
