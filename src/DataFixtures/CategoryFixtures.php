<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $data = [
            ['Kompozycje na Wszystkich Świętych', 'wszystkich-swietych', 'build/images/offer/wszystkich-swietych.jpg', 'Pamiętamy o naszych bliskich.', null, 1],
            ['Bukiety ślubne', 'bukiety-slubne', 'build/images/offer/bukiety-slubne.jpg', 'Nic nie zastąpi kwiatów.', null, 2],
            ['Bukiety okolicznościowe', 'bukiety-okolicznosciowe', 'build/images/offer/bukiety-okolicznosciowe.jpg', 'Każdy powód jest dobry.', null, 3],
            ['Wiązanki, wieńce i palmy pogrzebowe', 'wiazanki-wience-i-palmy-pogrzebowe', 'build/images/offer/wiazanki-wience-i-palmy-pogrzebowe.jpg', 'Spoczywaj w pokoju.', null, 4],
            ['Kompozycje kwiatowe', 'kompozycje-kwiatowe', 'build/images/offer/kompozycje-kwiatowe.jpg', null, null, 5],
            ['Wianki', 'wianki', 'build/images/offer/wianki.jpg', 'Cuda, wianki...', null, 6],
            ['Dekoracje kwiatowe', 'dekoracje-kwiatowe', 'build/images/offer/dekoracje-kwiatowe.jpg', null, null, 7],
            ['Kwiaty doniczkowe', 'kwiaty-doniczkowe', 'build/images/offer/kwiaty-doniczkowe.jpg', null, null, 8],
            ['Pakowanie prezentów', 'pakowanie-prezentow', 'build/images/offer/pakowanie-prezentow.jpg', 'Upominki, bibeloty, kartki okolicznościowe.', null, 9],
        ];
        foreach ($data as [$name, $slug, $image, $short, $long, $sort]) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($slug);
            $category->setImage($image);
            $category->setShortDescription($short);
            $category->setLongDescription($long);
            $category->setSort($sort);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
