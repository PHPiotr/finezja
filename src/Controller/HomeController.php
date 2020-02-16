<?php

namespace App\Controller;

use App\Entity\Category;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class HomeController extends AbstractController
{
    private function getEvent()
    {
        $events = [
            '21.01' => '21.01 Dzień Babci<br />22.01 Dzień Dziadka',
            '22.01' => '22.01 Dzień Dziadka',
            '14.02' => '14.03 Walentynki',
            '08.03' => '08.03 Dzień Kobiet',
            '13.04' => '12-13.04 Wielkanoc',
            '26.05' => '26.05 Dzień Matki',
            '23.06' => '26.05 Dzień Ojca',
            '30.09' => '30.09 Dzień Chłopaka',
            '14.10' => '14.10 Dzień Nauczyciela',
            '01.11' => '01.11 Wszystkich Świętych',
            '26.12' => '24-26.12 Boże Narodzenie',
        ];

        $currentEvent = null;
        foreach ($events as $eventKey => $eventValue) {
            $eventDate = Carbon::parse(Carbon::createFromFormat('d.m H:i:s', $eventKey . ' 23:59:59')->toDateString());
            if ($eventDate->gte(Carbon::now()->subDay())) {
                return $eventValue;
            }
        }

        return $events[array_keys($events)[0]];
    }

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
            'event' => $this->getEvent(),
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
