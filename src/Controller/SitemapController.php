<?php
namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findBy([], ['sort' => 'asc']);
        return $this->render("sitemap.{$request->getRequestFormat()}.twig", [
            'active' => 'sitemap',
            'metaTitle' => 'Mapa strony',
            'metaDescription' => '',
            'metaKeywords' => 'mapa strony,',
            'items' => $categories,
        ]);
    }
}
