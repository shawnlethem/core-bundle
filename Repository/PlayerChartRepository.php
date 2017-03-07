<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use VideoGamesRecords\CoreBundle\Tools\Ranking;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class PlayerChartRepository extends EntityRepository
{
    /**
     * @param array $params idChart|idPlayer|limit|maxRank
     * @todo
     * => Join etat to keep only boolRanking = 1
     * => If idPlayer, search for the rank and display a range of -5 and +5
     * @return array
     */
    public function getRanking($params = [])
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Chart $chart */
        $chart = $params['chart'];

        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc', 'pc');
        $rsm->addFieldResult('pc', 'idChart', 'idChart');
        $rsm->addFieldResult('pc', 'idPlayer', 'idPlayer');
        $rsm->addFieldResult('pc', 'rank', 'rank');
        $rsm->addFieldResult('pc', 'nbEqual', 'nbEqual');
        $rsm->addFieldResult('pc', 'pointChart', 'pointChart');
        $rsm->addFieldResult('pc', 'idEtat', 'idEtat');
        $rsm->addFieldResult('pc', 'dateModif', 'dateModif');
        //$rsm->addJoinedEntityResult('VideoGamesRecords\CoreBundle\Entity\Player' , 'p', 'pc', 'player');
        //$rsm->addFieldResult('u','pseudo','pseudo');
        //$rsm->addFieldResult('u','idMembre','idMembre');

        $fields = [];
        $orders = [];
        $where = [];
        $parameters = [];
        $columns = [];

        $fields[] = 'pc.*';
        $fields[] = 'u.*';

        $where[] = 'pc.idChart = :idChart';
        $parameters['idChart'] = $params['idChart'];
        foreach ($chart->getLibs() as $lib) {
            $columnName = "value_" . $lib->getIdLibChart();
            $fields[] = "(SELECT value FROM vgr_player_chartlib WHERE idLibChart=" . $lib->getIdLibChart() . " AND idPlayer = pc.idPlayer) AS $columnName";
            $orders[] = $columnName . " " . $lib->getType()->getOrderBy();
            $columns[] = $columnName;
            $rsm->addScalarResult($columnName, $columnName);
        }


        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idPlayer', $params))) {
            $where[] = '(pc.rank <= :maxRank OR pc.idPlayer = :idPlayer)';
            $parameters['maxRank'] = $params['maxRank'];
            $parameters['idLogin'] = $params['idLogin'];
        } else if (array_key_exists('maxRank', $params)) {
            $where[] = 'pc.rank <= :maxRank';
            $parameters['maxRank'] = $params['maxRank'];
        }

        $where[] = 'pc.rank IS NOT NULL'; //----- Disabeld post


        $sql = sprintf(
            "SELECT %s
            FROM vgr_player_chart pc INNER JOIN vgr_player u ON pc.idPlayer = u.idPlayer
            WHERE %s ORDER BY %s",
            implode(',', $fields),
            implode(' AND ', $where),
            implode(',', $orders)
        );

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        foreach ($parameters as $key => $value) {
            $query->setParameter($key, $value);
        }

        $result = $query->getResult();

        $list = [];
        foreach ($result as $row) {
            $list[] = $row;
        }
        $list = Ranking::addChartRank($list, $columns);

        return $list;
    }

    /**
     * @param int $idChart
     * @return array
     */
    public function maj($idChart)
    {
        $chart = $this->_em->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($idChart);
        $ranking = $this->getRanking(
            [
                'idChart' => $idChart,
                'chart' => $chart,
            ]
        );

        // @todo disabled post (Rank is null)

        //----- Return players id
        $players = array();

        //----- Array of pointChart
        $pointsChart = Ranking::arrayPointRecord(count($ranking));

        foreach ($ranking as $k => $row) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\PlayerChart $playerChart */
            $playerChart = $row['pc'];
            //----- If equal
            if ($playerChart->getNbEqual() == 1) {
                $pointChart = $pointsChart[$playerChart->getRank()];
            } else {
                $pointChart = (int)(
                    array_sum(
                        array_slice(array_values($pointsChart), $playerChart->getRank() - 1, $playerChart->getNbEqual())
                    ) / $playerChart->getNbEqual()
                );
            }
            $playerChart->setPointChart($pointChart);

            $this->_em->persist($playerChart);
            $this->_em->flush($playerChart);

            $players[] = $playerChart->getIdPlayer();
        }

        $chart->setStatusPlayer(Chart::STATUS_NORMAL);
        $this->getEntityManager()->persist($chart);
        $this->getEntityManager()->flush();

        return $players;
    }


    /**
     * @param array $params
     * @return array
     */
    public function getRows($params = [])
    {
        $query = $this->createQueryBuilder('pc');

        if (array_key_exists('idPlayer', $params)) {
            $query->where('pc.idPlayer= :idPlayer')
                ->setParameter('idPlayer', $params['idPlayer']);
        }

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if (array_key_exists('orderBy', $params)) {
            $query->orderBy($params['orderBy']['column'], $params['orderBy']['order']);
        }

        return $query->getQuery()->getResult();
    }
}
