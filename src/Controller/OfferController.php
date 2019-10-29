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
        $items = (new Offer())->getItems();
        foreach ($items as $item) {
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
            'items' => $items,
            'metaTitle' => 'Oferta',
            'metaDescription' => ucfirst($metaDescription) . '.',
            'metaKeywords' => $metaKeywords,
        ]);
    }
}
