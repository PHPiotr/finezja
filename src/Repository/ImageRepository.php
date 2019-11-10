<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function getAllSortedFromCategory(Category $category)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.Category = :category')
            ->setParameter('category', $category->getId())
            ->orderBy('i.sort', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByName($value): ?Image
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
