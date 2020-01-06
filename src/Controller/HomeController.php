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
        $categoriesForSlider = $repo->getForSlider();
        $filesystem = new Filesystem();
        $publicDir = $this->getParameter('public_directory');
        $categoriesSlides = [];
        foreach($categoriesForSlider as $category) {
            $id = $category->getId();
            $slide = "/images/slider/slide-{$id}.jpg";
            $categoriesSlides[$id] = $filesystem->exists("{$publicDir}/{$slide}") ? $slide : $category->getImage();
        }

        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findBy([], ['sort' => 'asc']);
        $itemsLeft = $itemsMiddle = $itemsRight = [];
        $itemsCount = count($categories);
        $i = 0;
        while($i < $itemsCount) {
            $left = $i;
            $middle = $i + 1;
            $right = $i + 2;
            if (isset($categories[$left])) {
                $itemsLeft[] = $categories[$left];
            }
            if (isset($categories[$middle])) {
                $itemsMiddle[] = $categories[$middle];
            }
            if (isset($categories[$right])) {
                $itemsRight[] = $categories[$right];
            }
            $i += 3;
        }

        return $this->render('home.html.twig', [
            'active' => 'home',
            'metaTitle' => 'Kwiaciarnia Finezja - Wodzisław Śląski',
            'metaDescription' => 'Bukiety ślubne, palmy pogrzebowe, stroiki, kompozycje, rośliny doniczkowe.',
            'metaKeywords' => 'bukiety,bukiety ślubne,ślubne,palmy pogrzebowe,pogrzebowe,rośliny doniczkowe,kwiaty cięte,kompozycje kwiatowe,',
            'categories' => $categoriesForSlider,
            'slides' => $categoriesSlides,

            'items_left' => $itemsLeft,
            'items_middle' => $itemsMiddle,
            'items_right' => $itemsRight,
            'items' => $categories,
        ]);
    }
}
