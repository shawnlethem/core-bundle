<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * PlayerGameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerGameRepository extends EntityRepository
{

    /**
     * @param array $params idJeu|idPlayer|limit|maxRank
     * @return array
     */
    public function getRankingPoints($params = [])
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankPoint');

        $query->where('pg.idGame= :idGame')
            ->setParameter('idGame', $params['idGame']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idPlayer', $params))) {
            $query->andWhere('(pg.rankPoint <= :maxRank OR pg.idPlayer = :idPlayer)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('idPlayer', $params['idPlayer']);
        } elseif (array_key_exists('maxRank', $params)) {
            $query->andWhere('pg.rankPoint <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param array $params idJeu|idPlayer|limit|maxRank
     * @return array
     */
    public function getRankingMedals($params = [])
    {
        $query = $this->createQueryBuilder('pg')
            ->join('pg.player', 'p')
            ->addSelect('p')//----- for using ->getPlayer() on each result
            ->orderBy('pg.rankMedal');

        $query->where('pg.idGame = :idGame')
            ->setParameter('idGame', $params['idGame']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idPlayer', $params))) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR pg.idPlayer = :idPlayer)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('idPlayer', $params['idPlayer']);
        } elseif (array_key_exists('maxRank', $params)) {
            $query->andWhere('pg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param $idGame
     */
    public function maj($idGame)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\PlayerGame pg WHERE pg.idGame = :idGame');
        $query->setParameter('idGame', $idGame);
        $query->execute();

        //----- data without DLC
        $query = $this->_em->createQuery("
            SELECT
                 pg.idPlayer,
                 SUM(pg.pointChart) as pointChartWithoutDlc,
                 SUM(pg.nbChart) as nbChartWithoutDlc,
                 SUM(pg.nbChartProven) as nbChartProvenWithoutDlc
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.group g
            WHERE g.idGame = :idGame
            AND g.boolDlc = 0
            GROUP BY pg.idPlayer");

        $dataWithoutDlc = [];

        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();
        foreach ($result as $row) {
            $dataWithoutDlc[$row['idPlayer']] = $row;
        }

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                pg.idPlayer,
                (g.idGame) as idGame,
                '' as rankPoint,
                '' as rankMedal,
                SUM(pg.rank0) as rank0,
                SUM(pg.rank1) as rank1,
                SUM(pg.rank2) as rank2,
                SUM(pg.rank3) as rank3,
                SUM(pg.rank4) as rank4,
                SUM(pg.rank5) as rank5,
                SUM(pg.pointChart) as pointChart,
                SUM(pg.nbChart) as nbChart,
                SUM(pg.nbChartProven) as nbChartProven
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerGroup pg
            JOIN pg.group g
            WHERE g.idGame = :idGame
            GROUP BY pg.idPlayer
            ORDER BY pointChart DESC");


        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $row = array_merge($row, $dataWithoutDlc[$row['idPlayer']]);
            $list[] = $row;
        }

        //----- add some data
        $list = Ranking::addRank($list, 'rankPoint', ['pointChart'], true);
        $list = Ranking::calculateGamePoints($list, ['rankPoint', 'nbEqual'], 'pointGame', 'pointChart');
        $list = Ranking::order($list, ['rank0' => 'DESC', 'rank1' => 'DESC', 'rank2' => 'DESC', 'rank3' => 'DESC']);
        $list = Ranking::addRank($list, 'rankMedal', ['rank0', 'rank1', 'rank2', 'rank3', 'rank4', 'rank5']);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer]);

        $game = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);

        foreach ($list as $row) {
            $playerGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\PlayerGame'
            );
            $playerGame->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $row['idPlayer']));
            $playerGame->setGame($game);

            $this->_em->persist($playerGame);
        }
        $this->_em->flush();
    }
}
