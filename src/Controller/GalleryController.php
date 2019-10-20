<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GalleryController extends AbstractController
{
    private $items = [

    ];

    public function indexAction()
    {
        return $this->render('gallery.html.twig', [
            'active' => 'gallery',
            'metaTitle' => 'Galeria',
            'metaDescription' => '',
            'metaKeywords' => '',
        ]);
    }
}
