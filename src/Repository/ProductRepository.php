<?php

namespace App\Repository;

use App\Domain\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Product[]
     */
    public function findWithSearch(Search $search) : array
    {
        $queryBuilder = $this
            ->createQueryBuilder('product')
            ->addSelect('category')
            ->leftjoin('product.category', 'category');

        if (!empty($search->categories)) {
            $this->searchByCategories($queryBuilder, $search->categories);
        }

        if (!is_null($search->string)) {
            $this->searchByName($queryBuilder, $search->string);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function searchByCategories(QueryBuilder $queryBuilder, $categories)
    {
        return $queryBuilder
            ->andWhere('category.id IN (:categories)')
            ->setParameter('categories', $categories);
    }

    public function searchByName(QueryBuilder $queryBuilder, $string)
    {
        return $queryBuilder
            ->andWhere('product.name LIKE :string')
            ->setParameter('string', "%{$string}%");
    }
}
