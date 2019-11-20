<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class HomeController extends AbstractController
{
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->getForSlider();
        $filesystem = new Filesystem();
        $publicDir = $this->getParameter('public_directory');
        $categoriesSlides = [];
        foreach($categories as $category) {
            $id = $category->getId();
            $slide = "/images/slider/slide-{$id}.jpg";
            $categoriesSlides[$id] = $filesystem->exists("{$publicDir}/{$slide}") ? $slide : $category->getImage();
        }
        return $this->render('home.html.twig', [
            'active' => 'home',
            'metaTitle' => 'Kwiaciarnia Finezja - Wodzisław Śląski',
            'metaDescription' => 'Bukiety ślubne, palmy pogrzebowe, stroiki, kompozycje, rośliny doniczkowe.',
            'metaKeywords' => 'bukiety,bukiety ślubne,ślubne,palmy pogrzebowe,pogrzebowe,rośliny doniczkowe,kwiaty cięte,kompozycje kwiatowe,',
            'categories' => $categories,
            'slides' => $categoriesSlides,
        ]);
    }
}
