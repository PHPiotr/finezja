<?php
namespace App\Controller;

use App\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $items = (new Offer())->getItems();
        return $this->render("sitemap.{$request->getRequestFormat()}.twig", [
            'active' => 'sitemap',
            'metaTitle' => 'Mapa strony',
            'metaDescription' => '',
            'metaKeywords' => 'mapa strony,',
            'items' => $items,
        ]);
    }
}
