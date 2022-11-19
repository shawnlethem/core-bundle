<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Player;

class PlayerRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param $q
     * @return mixed
     */
    public function autocomplete($q)
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->where('p.pseudo LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('p.pseudo', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * @param $user
     * @return mixed|Player
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function getPlayerFromUser($user)
    {
        $qb = $this->createQueryBuilder('player')
            ->where('player.user = :userId')
            ->setParameter('userId', $user->getId())
            ->addSelect('team')->leftJoin('player.team', 'team');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getStats()
    {
        $qb = $this->createQueryBuilder('player')
            ->select('COUNT(player.id), SUM(player.nbChart), SUM(player.nbChartProven)');
        $qb->where('player.nbChart > 0');

        return $qb->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @return int|mixed|string
     */
    public function getProofStats()
    {
        $query = $this->createQueryBuilder('player')
            ->select('player.id as idPlayer, player.pseudo')
            ->innerJoin('player.proofRespondings', 'proof')
            ->addSelect('COUNT(proof.id) as nb, SUBSTRING(proof.updatedAt, 1, 7) as month')
            ->where("proof.checkedAt > '2020-01-01'")
            ->orderBy('month', 'DESC')
            ->groupBy('player.id')
            ->addGroupBy('month');
        return $query->getQuery()->getResult(2);
    }
}
