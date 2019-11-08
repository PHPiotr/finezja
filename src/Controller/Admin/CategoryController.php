<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
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
    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findBy([], ['sort' => 'ASC']);

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'slug' => $category->getSlug(),
                'name' => $category->getName(),
                'sort' => $category->getSort(),
                'image' => $category->getImage(),
            ];
        }

        return $this->render('admin/categories/list.html.twig', [
            'categories' => $data,
            'categoriesJson' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * @Route("/admin/oferta/nowa-kategoria", name="newCategory")
     */
    public function newAction()
    {
        return $this->render('admin/categories/new.html.twig', []);
    }

    /**
     * @Route("/admin/categories/add", methods={"POST"}, name="addCategory")
     * @param ValidatorInterface $validator
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(ValidatorInterface $validator, Request $request)
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
                $image->setSort($imagesSort++);

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

        return $this->json([
            'succeededUploads' => $succeeded,
            'failedUploads' => $failedUploads,
            'category' => [
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
                        'sort' => $image->getSort(),
                    ];
                }, $category->getImages()->toArray())),
            ],
        ]);
    }

    /**
     * @Route("/admin/categories/{id}", methods={"POST"}, name="editCategory")
     * @param Category $category
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function editAction(Category $category, Request $request, ValidatorInterface $validator)
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
                $image->setSort($imagesSort++);

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
        $imagesToRemove = json_decode($request->request->get('imagesToRemove'));
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
            'category' => [
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
                        'sort' => $image->getSort(),
                    ];
                }, $category->getImages()->toArray())),
            ],
        ]);
    }

    /**
     * @Route("/admin/oferta/{id}", name="adminCategory")
     * @param Category $category
     * @return Response
     */
    public function renderEditAction(Category $category)
    {
        return $this->render('admin/categories/edit.html.twig', ['category' => json_encode([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'shortDescription' => $category->getShortDescription(),
            'longDescription' => $category->getLongDescription(),
            'image' => $category->getImage(),
            'sort' => $category->getSort(),
            'images' => array_values(array_map(function($image) {
                return [
                    'name' => $image->getName(),
                    'sort' => $image->getSort(),
                ];
            }, $category->getImages()->toArray())),
        ], JSON_UNESCAPED_UNICODE)]);
    }

    /**
     * @Route("/admin/categories/sort", methods={"PUT"}, name="adminSortCategories")
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    public function sortAction(Request $request)
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
     * @throws \Exception
     */
    public function deleteCategory(Category $category)
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
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
