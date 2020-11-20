<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

/**
 * Class GameController
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @return Player|null
     */
    private function getPlayer()
    {
        if ($this->getUser() !== null) {
            return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                               ->getPlayerFromUser($this->getUser());
        }
        return null;
    }

    /**
     * @return Team|null
     */
    private function getTeam()
    {
        if ($this->getUser() !== null) {
            $player =  $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Player')
                ->getPlayerFromUser($this->getUser());
            return $player->getTeam();
        }
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listByLetter(Request $request)
    {
        $letter = $request->query->get('letter', '0');
        $locale = $request->getLocale();
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')
            ->findWithLetter($letter, $locale)
            ->getResult();
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function listFromPlayerLostPosition(Request $request)
    {
        $locale = $request->getLocale();
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')
            ->findFromlostPosition($this->getPlayer(), $locale);
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function playerRankingPoints(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        $idTeam = $request->query->get('idTeam', null);
        if ($idTeam) {
            $team = $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam);
        } else {
            $team = null;
        }
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingPoints($game, $maxRank, $this->getPlayer(), $team);
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function playerRankingMedals(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:PlayerGame')->getRankingMedals($game, $maxRank, $this->getPlayer());
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function teamRankingPoints(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingPoints($game, $maxRank, $this->getTeam());
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function teamRankingMedals(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:TeamGame')->getRankingMedals($game, $maxRank, $this->getTeam());
    }



    /**
     * @Route("/rss", name="game_rss")
     * @Method("GET")
     * @Cache(smaxage="10")
     * @return Response
     */
    public function rssAction()
    {
        $games = $this->getDoctrine()->getRepository('VideoGamesRecordsCoreBundle:Game')->findBy(
            array(
                'status' => 'ACTIF'
            ),
            array('publishedAt' => 'DESC'),
            20
        );

        $feed = $this->get('eko_feed.feed.manager')->get('game');

        // Add prefixe link
        foreach ($games as $game) {
            $game->setLink($feed->get('link') . $game->getId() . '/' . $game->getSlug());
        }

        $feed->addFromArray($games);

        return new Response($feed->render('rss'));
    }
}
