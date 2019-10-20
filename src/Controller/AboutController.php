<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AboutController extends AbstractController
{

    public function indexAction()
    {
        return $this->render('about.html.twig', [
            'active' => 'about',
            'metaTitle' => 'O nas',
            'metaDescription' => 'Kwiaty na urodziny, rocznice, itd. Upominki na dowolną okazję od 2006 r.',
            'metaKeywords' => 'dostawa kwiatów,upominki,bukiety,kompozycje,urodziny,rocznice,',
        ]);
    }
}
