<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OfferController extends AbstractController
{
    private $items = [
        'wszystkich-swietych' => [
            'title' => 'Kompozycje na Wszystkich Świętych',
            'img' => 'build/images/offer/wszystkich-swietych.jpg',
            'slug' => 'wszystkich-swietych',
            'description' => 'Pamiętamy o naszych bliskich.',
            'images' => [
                'build/images/offer/wszystkich-swietych.jpg',
                'build/images/offer/wszystkich-swietych_1.jpg',
                'build/images/offer/wszystkich-swietych_2.jpg',
                'build/images/offer/wszystkich-swietych_3.jpg',
                'build/images/offer/wszystkich-swietych_4.jpg',
                'build/images/offer/wszystkich-swietych_5.jpg',
                'build/images/offer/wszystkich-swietych_6.jpg',
                'build/images/offer/wszystkich-swietych_7.jpg',
                'build/images/offer/wszystkich-swietych_8.jpg',
                'build/images/offer/wszystkich-swietych_9.jpg',
                'build/images/offer/wszystkich-swietych_10.jpg',
                'build/images/offer/wszystkich-swietych_11.jpg',
                'build/images/offer/wszystkich-swietych_12.jpg',
                'build/images/offer/wszystkich-swietych_13.jpg',
                'build/images/offer/wszystkich-swietych_14.jpg',
                'build/images/offer/wszystkich-swietych_15.jpg',
                'build/images/offer/wszystkich-swietych_16.jpg',
                'build/images/offer/wszystkich-swietych_17.jpg',
                'build/images/offer/wszystkich-swietych_18.jpg',
                'build/images/offer/wszystkich-swietych_19.jpg',
                'build/images/offer/wszystkich-swietych_20.jpg',
            ],
        ],
        'bukiety-slubne' => [
            'title' => 'Bukiety ślubne',
            'img' => 'build/images/offer/bukiety-slubne.jpg',
            'slug' => 'bukiety-slubne',
            'description' => 'Nic nie zastąpi kwiatów.',
            'images' => [
                'build/images/offer/bukiety-slubne.jpg',
            ],
        ],
        'bukiety-okolicznosciowe' => [
            'title' => 'Bukiety okolicznościowe',
            'img' => 'build/images/offer/bukiety-okolicznosciowe.jpg',
            'slug' => 'bukiety-okolicznosciowe',
            'description' => 'Każdy powód jest dobry.',
            'images' => [
                'build/images/offer/bukiety-okolicznosciowe.jpg',
                'build/images/offer/bukiety-okolicznosciowe-1.jpg',
                'build/images/offer/bukiety-okolicznosciowe-2.jpg',
                'build/images/offer/bukiety-okolicznosciowe-3.jpg',
                'build/images/offer/bukiety-okolicznosciowe-4.jpg',
                'build/images/offer/bukiety-okolicznosciowe-5.jpg',
                'build/images/offer/bukiety-okolicznosciowe-6.jpg',
                'build/images/offer/bukiety-okolicznosciowe-7.jpg',
            ],
        ],
        'wiazanki-wience-i-palmy-pogrzebowe' => [
            'title' => 'Wiązanki, wieńce i palmy pogrzebowe',
            'img' => 'build/images/offer/wiazanki-wience-i-palmy-pogrzebowe.jpg',
            'slug' => 'wiazanki-wience-i-palmy-pogrzebowe',
            'description' => 'Spoczywaj w pokoju.',
            'images' => [
                'build/images/offer/wiazanki-wience-i-palmy-pogrzebowe.jpg',
            ],
        ],
        'kompozycje-kwiatowe' => [
            'title' => 'Kompozycje kwiatowe',
            'img' => 'build/images/offer/kompozycje-kwiatowe.jpg',
            'slug' => 'kompozycje-kwiatowe',
            'description' => '',
            'images' => [
                'build/images/offer/kompozycje-kwiatowe.jpg',
                'build/images/offer/kompozycje-kwiatowe-1.jpg',
                'build/images/offer/kompozycje-kwiatowe-2.jpg',
                'build/images/offer/kompozycje-kwiatowe-3.jpg',
                'build/images/offer/kompozycje-kwiatowe-4.jpg',
                'build/images/offer/kompozycje-kwiatowe-5.jpg',
                'build/images/offer/kompozycje-kwiatowe-6.jpg',
            ],
        ],
        'wianki' => [
            'title' => 'Wianki',
            'img' => 'build/images/offer/wianki.jpg',
            'slug' => 'wianki',
            'description' => 'Cuda, wianki...',
            'images' => [
                'build/images/offer/wianki.jpg',
                'build/images/offer/wianki_1.jpg',
                'build/images/offer/wianki_2.jpg',
                'build/images/offer/wianki_3.jpg',
                'build/images/offer/wianki_4.jpg',
            ],
        ],
        'dekoracje-kwiatowe' => [
            'title' => 'Dekoracje kwiatowe',
            'img' => 'build/images/offer/dekoracje-kwiatowe.jpg',
            'slug' => 'dekoracje-kwiatowe',
            'description' => '',
            'images' => [
                'build/images/offer/dekoracje-kwiatowe.jpg',
            ],
        ],
        'kwiaty-doniczkowe' => [
            'title' => 'Kwiaty doniczkowe',
            'img' => 'build/images/offer/kwiaty-doniczkowe.jpg',
            'slug' => 'kwiaty-doniczkowe',
            'description' => '',
            'images' => [
                'build/images/offer/kwiaty-doniczkowe.jpg',
                'build/images/offer/kwiaty-doniczkowe-1.jpg',
            ],
        ],
        'pakowanie-prezentow' => [
            'title' => 'Pakowanie prezentów',
            'img' => 'build/images/offer/pakowanie-prezentow.jpg',
            'slug' => 'pakowanie-prezentow',
            'description' => 'Upominki, bibeloty, kartki okolicznościowe.',
            'images' => [
                'build/images/offer/pakowanie-prezentow.jpg',
            ],
        ],
    ];

    public function viewAction(string $slug)
    {
        if (!isset($this->items[$slug])) {
            throw new Exception('Missing category for ' . $slug);
        }
        $item = $this->items[$slug] ?? null;
        if (!$item) {
            throw new NotFoundHttpException('Nie ma takiej kategorii w ofercie.');
        }
        $title = $item['title'];
        $description = $item['description'];
        return $this->render('offer.html.twig', [
            'active' => 'offer',
            'item' => $item,
            'metaTitle' => $title,
            'metaDescription' => sprintf('%s.%s', $title, !empty($description) ? " $description." : ''),
            'metaKeywords' => $title . ',',
        ]);
    }

    public function indexAction()
    {
        $metaDescription = '';
        $metaKeywords = '';
        $index = 0;
        foreach ($this->items as $item) {
            $lowerTitle = mb_strtolower($item['title']);
            $metaKeywords .= $lowerTitle . ',';
            if ($index++ > 2) {
                continue;
            }
            $metaDescription .= $lowerTitle . ', ';
        }
        $metaDescription = rtrim($metaDescription, ', ');
        return $this->render('offers.html.twig', [
            'active' => 'offer',
            'items' => $this->items,
            'metaTitle' => 'Oferta',
            'metaDescription' => ucfirst($metaDescription) . '.',
            'metaKeywords' => $metaKeywords,
        ]);
    }
}
