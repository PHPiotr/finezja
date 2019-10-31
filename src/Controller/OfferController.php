<?php

namespace App\Controller;

use App\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OfferController extends AbstractController
{
    public function viewAction(string $slug)
    {
        $items = (new Offer())->getItems();
        if (!isset($items[$slug])) {
            throw new Exception('Missing category for ' . $slug);
        }
        $item = $items[$slug] ?? null;
        if (!$item) {
            throw new NotFoundHttpException('Nie ma takiej kategorii w ofercie.');
        }
        $title = $item['title'];
        $description = $item['description'];

        $imagesLeft = [];
        $imagesMiddle = [];
        $imagesRight = [];
        $images = $item['images'];
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
            'item' => $item,
            'images_left' => $imagesLeft,
            'images_middle' => $imagesMiddle,
            'images_right' => $imagesRight,
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
        $items = array_values((new Offer())->getItems());
        foreach ($items as $item) {
            $lowerTitle = mb_strtolower($item['title']);
            $metaKeywords .= $lowerTitle . ',';
            if ($index++ > 2) {
                continue;
            }
            $metaDescription .= $lowerTitle . ', ';
        }
        $metaDescription = rtrim($metaDescription, ', ');

        $itemsLeft = $itemsMiddle = $itemsRight = [];
        $itemsCount = count($items);
        $i = 0;
        while($i < $itemsCount) {
            $left = $i;
            $middle = $i + 1;
            $right = $i + 2;
            if (isset($items[$left])) {
                $itemsLeft[] = $items[$left];
            }
            if (isset($items[$middle])) {
                $itemsMiddle[] = $items[$middle];
            }
            if (isset($items[$right])) {
                $itemsRight[] = $items[$right];
            }
            $i += 3;
        }

        return $this->render('offers.html.twig', [
            'active' => 'offer',
            'items_left' => $itemsLeft,
            'items_middle' => $itemsMiddle,
            'items_right' => $itemsRight,
            'metaTitle' => 'Oferta',
            'metaDescription' => ucfirst($metaDescription) . '.',
            'metaKeywords' => $metaKeywords,
        ]);
    }
}
