<?php

namespace AppBundle\Repository;

use AppBundle\Entity\CityZip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CityZipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CityZip::class);
    }
    
    /**
     * @param string $query
     * @param string $country
     * @param int $limit
     * @return CityZip[]
     */
    public function findAllMatching(string $query, string $country, int $limit = 5)
    {
        return $this->createQueryBuilder('cz')
            ->select('cz.id')
            ->addSelect("CONCAT(cz.zipCode, ' ', c.name) AS zip_code_city")
            ->leftJoin('cz.city', 'c')
            ->andWhere('c.country = :country')
            ->andWhere('cz.zipCode LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setParameter('country', $country)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
