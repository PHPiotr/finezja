<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ];
        }

        return $this->render('admin/categories/list.html.twig', ['categories' => $data]);
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
     * @return JsonResponse
     */
    public function addAction(ValidatorInterface $validator)
    {
        $request = Request::createFromGlobals();

        $slugify = new Slugify();

        $category = new Category();
        $category->setName($request->request->get('name'));
        $category->setSlug($slugify->slugify($category->getName()));
        $category->setShortDescription($request->request->get('shortDescription'));
        $category->setLongDescription($request->request->get('longDescription'));
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
        $key = 0;
        $mainCategoryImageName = '';
        $succeeded = [];
        foreach ($uploadedFiles as $uploadedFile) {
            $clientOriginalName = $uploadedFile->getClientOriginalName();
            $originalFilename = pathinfo($clientOriginalName, PATHINFO_FILENAME);
            $originalExtension = pathinfo($clientOriginalName, PATHINFO_EXTENSION);
            $newFilename = sprintf('%s%s.%s', $slug, $key === 0 ? '' : "-{$key}", $originalExtension);
            $key++;
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
            if (isset($failedUploads[$clientOriginalName])) {

            } else {
                $image = new Image();
                $image->setName("/images/offer/{$newFilename}");
                $image->setSort($imagesSort++);
                $image->setCategory($category);

                $errors = $validator->validate($image);
                if (count($errors) > 0) {
                    return $this->json(['errors' => $errors], 400);
                }

                $entityManager->persist($image);
            }
        }

        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 400);
        }
        if (!$succeeded) {
            return $this->json(['not ok']);
        }

        if (isset($failedUploads[$mainCategoryImageName])) {
            $category->setImage('/images/offer/'.$succeeded[0]);
        }
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'succeededUploads' => $succeeded,
            'failedUploads' => $failedUploads,
            'category' => [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'slug' => $category->getSlug(),
                'image' => $category->getImage(),
                'images' => $category->getImages(),
                'sort' => $category->getSort(),
            ],
        ]);
    }

    /**
     * @Route("/admin/oferta/{id}", name="adminCategory")
     * @param Category $category
     * @return Response
     */
    public function editAction(Category $category)
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findBy([], ['sort' => 'ASC']);

        return $this->render('admin/categories.html.twig', ['categories' => $categories]);
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
}
