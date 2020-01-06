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
        return $this->redirectToRoute('index');
    }
}
