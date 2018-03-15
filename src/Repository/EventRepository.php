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

    /**
     * Retourne la liste des évènement les plus récents
     * @param int $n
     * @return Event[]
     */
    public function findLastAddedEvents($n = 5)
    {
        $dbh = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT    e.*
            FROM      event e
            ORDER BY  e.created_date DESC
            LIMIT  ${n}";
        $stmt = $dbh->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Retourne la liste des évènements correspondants à la recherche
     * Va chercher dans le nom et la description
     * @param array $words
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function findSearchResult($words, $offset = 0, $limit = null)
    {

        $builder = $this->createQueryBuilder('e');

        // Mise en place des conditions
        for ($i = 0; $i < count($words); $i++) {
            $param = "?${i}";
            $builder->andWhere($builder->expr()->orX(
                $builder->expr()->like('e.name', $param),
                $builder->expr()->like('e.description', $param)
            ));
        }

        // On inclut les paramètres
        for ($i = 0; $i < count($words); $i++) {
            $builder->setParameter($i, '%' . $words[$i] . '%');
        }

        // Offset
        $builder->setFirstResult($offset);

        // Limite
        if ($limit) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery()
            ->getResult();
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
