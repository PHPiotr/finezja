<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = [
            ['build/images/offer/wszystkich-swietych.jpg', 'wszystkich-swietych', 1],
            ['build/images/offer/wszystkich-swietych_22.jpg', 'wszystkich-swietych', 2],
            ['build/images/offer/wszystkich-swietych_23.jpg', 'wszystkich-swietych', 3],
            ['build/images/offer/wszystkich-swietych_24.jpg', 'wszystkich-swietych', 4],
            ['build/images/offer/wszystkich-swietych_25.jpg', 'wszystkich-swietych', 5],
            ['build/images/offer/wszystkich-swietych_26.jpg', 'wszystkich-swietych', 6],
            ['build/images/offer/wszystkich-swietych_27.jpg', 'wszystkich-swietych', 7],
            ['build/images/offer/wszystkich-swietych_28.jpg', 'wszystkich-swietych', 8],
            ['build/images/offer/wszystkich-swietych_29.jpg', 'wszystkich-swietych', 9],
            ['build/images/offer/wszystkich-swietych_30.jpg', 'wszystkich-swietych', 10],
            ['build/images/offer/wszystkich-swietych_31.jpg', 'wszystkich-swietych', 11],
            ['build/images/offer/wszystkich-swietych_1.jpg', 'wszystkich-swietych', 12],
            ['build/images/offer/wszystkich-swietych_2.jpg', 'wszystkich-swietych', 13],
            ['build/images/offer/wszystkich-swietych_3.jpg', 'wszystkich-swietych', 14],
            ['build/images/offer/wszystkich-swietych_4.jpg', 'wszystkich-swietych', 15],
            ['build/images/offer/wszystkich-swietych_5.jpg', 'wszystkich-swietych', 16],
            ['build/images/offer/wszystkich-swietych_6.jpg', 'wszystkich-swietych', 17],
            ['build/images/offer/wszystkich-swietych_7.jpg', 'wszystkich-swietych', 18],
            ['build/images/offer/wszystkich-swietych_8.jpg', 'wszystkich-swietych', 19],
            ['build/images/offer/wszystkich-swietych_9.jpg', 'wszystkich-swietych', 20],
            ['build/images/offer/wszystkich-swietych_10.jpg', 'wszystkich-swietych', 21],
            ['build/images/offer/wszystkich-swietych_11.jpg', 'wszystkich-swietych', 22],
            ['build/images/offer/wszystkich-swietych_12.jpg', 'wszystkich-swietych', 23],
            ['build/images/offer/wszystkich-swietych_13.jpg', 'wszystkich-swietych', 24],
            ['build/images/offer/wszystkich-swietych_14.jpg', 'wszystkich-swietych', 25],
            ['build/images/offer/wszystkich-swietych_15.jpg', 'wszystkich-swietych', 26],
            ['build/images/offer/wszystkich-swietych_16.jpg', 'wszystkich-swietych', 27],
            ['build/images/offer/wszystkich-swietych_17.jpg', 'wszystkich-swietych', 28],
            ['build/images/offer/wszystkich-swietych_18.jpg', 'wszystkich-swietych', 29],
            ['build/images/offer/wszystkich-swietych_19.jpg', 'wszystkich-swietych', 30],
            ['build/images/offer/wszystkich-swietych_20.jpg', 'wszystkich-swietych', 31],
            ['build/images/offer/wszystkich-swietych_21.jpg', 'wszystkich-swietych', 32],
            ['build/images/offer/bukiety-slubne.jpg', 'bukiety-slubne', 33],
            ['build/images/offer/bukiety-slubne-1.jpg', 'bukiety-slubne', 34],
            ['build/images/offer/bukiety-slubne-2.jpg', 'bukiety-slubne', 35],
            ['build/images/offer/bukiety-slubne-3.jpg', 'bukiety-slubne', 36],
            ['build/images/offer/bukiety-slubne-4.jpg', 'bukiety-slubne', 37],
            ['build/images/offer/bukiety-slubne-5.jpg', 'bukiety-slubne', 38],
            ['build/images/offer/bukiety-okolicznosciowe.jpg', 'bukiety-okolicznosciowe', 39],
            ['build/images/offer/bukiety-okolicznosciowe-1.jpg', 'bukiety-okolicznosciowe', 40],
            ['build/images/offer/bukiety-okolicznosciowe-2.jpg', 'bukiety-okolicznosciowe', 41],
            ['build/images/offer/bukiety-okolicznosciowe-3.jpg', 'bukiety-okolicznosciowe', 42],
            ['build/images/offer/bukiety-okolicznosciowe-4.jpg', 'bukiety-okolicznosciowe', 43],
            ['build/images/offer/bukiety-okolicznosciowe-5.jpg', 'bukiety-okolicznosciowe', 44],
            ['build/images/offer/bukiety-okolicznosciowe-6.jpg', 'bukiety-okolicznosciowe', 45],
            ['build/images/offer/bukiety-okolicznosciowe-7.jpg', 'bukiety-okolicznosciowe', 46],
            ['build/images/offer/wiazanki-wience-i-palmy-pogrzebowe.jpg', 'wiazanki-wience-i-palmy-pogrzebowe', 47],
            ['build/images/offer/kompozycje-kwiatowe.jpg', 'kompozycje-kwiatowe', 48],
            ['build/images/offer/kompozycje-kwiatowe-1.jpg', 'kompozycje-kwiatowe', 49],
            ['build/images/offer/kompozycje-kwiatowe-2.jpg', 'kompozycje-kwiatowe', 50],
            ['build/images/offer/kompozycje-kwiatowe-3.jpg', 'kompozycje-kwiatowe', 51],
            ['build/images/offer/kompozycje-kwiatowe-4.jpg', 'kompozycje-kwiatowe', 52],
            ['build/images/offer/kompozycje-kwiatowe-5.jpg', 'kompozycje-kwiatowe', 53],
            ['build/images/offer/kompozycje-kwiatowe-6.jpg', 'kompozycje-kwiatowe', 54],
            ['build/images/offer/wianki.jpg', 'wianki', 55],
            ['build/images/offer/wianki_1.jpg', 'wianki', 56],
            ['build/images/offer/wianki_2.jpg', 'wianki', 57],
            ['build/images/offer/wianki_3.jpg', 'wianki', 58],
            ['build/images/offer/wianki_4.jpg', 'wianki', 59],
            ['build/images/offer/dekoracje-kwiatowe.jpg', 'dekoracje-kwiatowe', 60],
            ['build/images/offer/kwiaty-doniczkowe.jpg', 'kwiaty-doniczkowe', 61],
            ['build/images/offer/kwiaty-doniczkowe-1.jpg', 'kwiaty-doniczkowe', 62],
            ['build/images/offer/pakowanie-prezentow.jpg', 'pakowanie-prezentow', 63],
        ];

        $repo = $manager->getRepository(Category::class);
        foreach ($data as [$name, $categorySlug, $sort]) {
            $image = new Image();
            $image->setName($name);
            $image->setSort($sort);
            $image->setCategory($repo->findOneBy(['slug' => $categorySlug]));
            $manager->persist($image);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}
