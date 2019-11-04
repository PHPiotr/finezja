<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OfferController extends AbstractController
{
    public function viewAction(Category $category)
    {
        $images = $category->getImages();

        $title = $category->getName();
        $description = $category->getShortDescription();

        $imagesLeft = [];
        $imagesMiddle = [];
        $imagesRight = [];
        $imagesLength = count($images);
        $i = 0;
        while ($i < $imagesLength) {
            $left = $i;
            $middle = $i + 1;
            $right = $i + 2;
            if (isset($images[$left])) {
                $imagesLeft[] = $images[$left];
            }
            if (isset($images[$middle])) {
                $imagesMiddle[] = $images[$middle];
            }
            if (isset($images[$right])) {
                $imagesRight[] = $images[$right];
            }
            $i += 3;
        }

        return $this->render('offer.html.twig', [
            'active' => 'offer',
            'item' => $category,
            'images_left' => $imagesLeft,
            'images_middle' => $imagesMiddle,
            'images_right' => $imagesRight,
            'images' => $images,
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
        $repo = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findBy([], ['sort' => 'asc']);
        foreach ($categories as $category) {
            $lowerName = mb_strtolower($category->getName());
            $metaKeywords .= $lowerName . ',';
            if ($index++ > 2) {
                continue;
            }
            $metaDescription .= $lowerName . ', ';
        }
        $metaDescription = rtrim($metaDescription, ', ');

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

        return $this->render('offers.html.twig', [
            'active' => 'offer',
            'items_left' => $itemsLeft,
            'items_middle' => $itemsMiddle,
            'items_right' => $itemsRight,
            'items' => $categories,
            'metaTitle' => 'Oferta',
            'metaDescription' => ucfirst($metaDescription) . '.',
            'metaKeywords' => $metaKeywords,
        ]);
    }
}
