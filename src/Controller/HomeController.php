<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->getForSlider();
        return $this->render('home.html.twig', [
            'active' => 'home',
            'metaTitle' => 'Kwiaciarnia Finezja - Wodzisław Śląski',
            'metaDescription' => 'Bukiety ślubne, palmy pogrzebowe, stroiki, kompozycje, rośliny doniczkowe.',
            'metaKeywords' => 'bukiety,bukiety ślubne,ślubne,palmy pogrzebowe,pogrzebowe,rośliny doniczkowe,kwiaty cięte,kompozycje kwiatowe,',
            'categories' => $categories,
        ]);
    }
}
