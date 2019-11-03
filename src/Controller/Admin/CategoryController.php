<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

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

        return $this->render('admin/categories.html.twig', ['categories' => $data]);
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
