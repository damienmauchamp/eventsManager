<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Retourne la liste des évènement ayant le plus participants
     * @param int $n
     * @return Event[]
     */
    public function findMostPopularEvents($n = 5): array
    {
        $dbh = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT    e.*, COUNT(eu.user_id) AS n
            FROM      event e
            LEFT JOIN event_user eu
              ON      eu.event_id = e.id_event
            GROUP BY  e.id_event
            ORDER BY  n DESC
            LIMIT  ${n}";
        $stmt = $dbh->query($sql);
        return $stmt->fetchAll();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('e')
            ->where('e.something = :value')->setParameter('value', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
