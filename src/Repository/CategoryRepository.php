<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function sortCategories(Category $category, int $oldSort, int $newSort)
    {
        if ($newSort === $oldSort) {
            return;
        }
        $entityManager = $this->getEntityManager();
        $conn = $entityManager->getConnection();
        try {
            $conn->beginTransaction();
            if ($newSort > $oldSort) {
                $sql = 'UPDATE categories SET sort = sort - 1 WHERE sort <= :newSort AND sort >= :oldSort';
            }
            if ($oldSort > $newSort) {
                $sql = 'UPDATE categories SET sort = sort + 1 WHERE sort >= :newSort AND sort <= :oldSort';
            }
            if (isset($sql)) {
                $stmt = $conn->prepare($sql);
                $stmt->execute(['newSort' => $newSort, 'oldSort' => $oldSort]);
            }
            $category->setSort($newSort);
            $entityManager->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function getMaxSort()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT sort FROM categories c
            ORDER BY c.sort DESC
            LIMIT 1
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getForSlider()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.sort', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
            ;
    }
}
