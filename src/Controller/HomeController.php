<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function indexAction()
    {
        return $this->render('home.html.twig', [
            'active' => 'home',
            'metaTitle' => 'Wodzisław',
            'metaDescription' => 'Bukiety ślubne, palmy pogrzebowe, stroiki, kompozycje, rośliny doniczkowe.',
            'metaKeywords' => 'bukiety,bukiety ślubne,ślubne,palmy pogrzebowe,pogrzebowe,rośliny doniczkowe,kwiaty cięte,kompozycje kwiatowe,',
        ]);
    }
}
