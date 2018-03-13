<?php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Label::class);
    }

    /**
     * Retourne la liste des catégories avec leurs IDs, noms et le nombre d'utilisations
     * {id, name, n}
     * @param int $n
     * @return Label[]
     */
    public function findMostUsed($n = 5): array
    {
        $dbh = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT    l.id_label AS id, l.name AS name, COUNT(e.label_id) AS n
            FROM      label l
            LEFT JOIN event_label e
            ON        e.label_id = l.id_label
            GROUP BY  l.id_label
            ORDER BY  n DESC
            LIMIT  ${n}";
        $stmt = $dbh->query($sql);
        return $stmt->fetchAll();
    }


    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('l')
            ->where('l.something = :value')->setParameter('value', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
