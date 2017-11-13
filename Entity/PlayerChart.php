<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\ProofBundle\Entity\Proof;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use VideoGamesRecords\CoreBundle\Model\Player\Player;

/**
 * PlayerChart
 *
 * @ORM\Table(name="vgr_player_chart", indexes={@ORM\Index(name="idxIdChart", columns={"idChart"}), @ORM\Index(name="idxIdPlayer", columns={"idPlayer"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PlayerChart
{
    use Timestampable;
    use Player;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPlayerChart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPlayerChart;

    /**
     * @ORM\Column(name="idChart", type="integer")
     */
    private $idChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=false)
     */
    private $rank;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbEqual", type="integer", nullable=false)
     */
    private $nbEqual = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="float", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="idStatus", type="integer", nullable=false)
     */
    private $idStatus;

    /**
     * @ORM\Column(name="idProof", type="integer")
     */
    private $idProof;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isTopScore", type="boolean", nullable=false)
     */
    private $topScore = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModif", type="datetime", nullable=false)
     */
    private $dateModif;



    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id")
     * })
     */
    private $chart;

    /**
     * @var Proof
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\ProofBundle\Entity\Proof")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProof", referencedColumnName="idProof")
     * })
     */
    private $proof;

    /**
     * @var Status
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatus", referencedColumnName="idStatus")
     * })
     */
    private $status;

    /**
     * Set idPlayerChart
     *
     * @param integer $idPlayerChart
     * @return PlayerChart
     */
    public function setIdPlayerChart($idPlayerChart)
    {
        $this->idPlayerChart = $idPlayerChart;
        return $this;
    }

    /**
     * Get idPlayerChart
     *
     * @return integer
     */
    public function getIdPlayerChart()
    {
        return $this->idPlayerChart;
    }

    /**
     * Set idChart
     *
     * @param integer $idChart
     * @return PlayerChart
     */
    public function setIdChart($idChart)
    {
        $this->idChart = $idChart;
        return $this;
    }

    /**
     * Get idChart
     *
     * @return integer
     */
    public function getIdChart()
    {
        return $this->idChart;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return PlayerChart
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set nbEqual
     *
     * @param integer $nbEqual
     * @return PlayerChart
     */
    public function setNbEqual($nbEqual)
    {
        $this->nbEqual = $nbEqual;
        return $this;
    }

    /**
     * Get nbEqual
     *
     * @return integer
     */
    public function getNbEqual()
    {
        return $this->nbEqual;
    }

    /**
     * Set pointChart
     *
     * @param float $pointChart
     * @return PlayerChart
     */
    public function setPointChart($pointChart)
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return float
     */
    public function getPointChart()
    {
        return $this->pointChart;
    }

    /**
     * Set idStatus
     *
     * @param integer $idStatus
     * @return PlayerChart
     */
    public function setIdStatus($idStatus)
    {
        $this->idStatus = $idStatus;
        return $this;
    }

    /**
     * Get idStatus
     *
     * @return integer
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * Set idProof
     *
     * @param integer $idProof
     * @return PlayerChart
     */
    public function setIdProof($idProof)
    {
        $this->idProof = $idProof;
        return $this;
    }

    /**
     * Get idProof
     *
     * @return integer
     */
    public function getIdProof()
    {
        return $this->idProof;
    }

    /**
     * Set topScore
     *
     * @param integer $topScore
     * @return PlayerChart
     */
    public function setTopScore($topScore)
    {
        $this->topScore = $topScore;
        return $this;
    }

    /**
     * Get topScore
     *
     * @return integer
     */
    public function getTopScore()
    {
        return $this->topScore;
    }

    /**
     * Set dateModif
     *
     * @param \DateTime $dateModif
     * @return PlayerChart
     */
    public function setDateModif($dateModif)
    {
        $this->dateModif = $dateModif;
        return $this;
    }

    /**
     * Get dateModif
     *
     * @return \DateTime
     */
    public function getDateModif()
    {
        return $this->dateModif;
    }

    /**
     * Set chart
     *
     * @param Chart $chart
     * @return PlayerChart
     */
    public function setChart(Chart $chart = null)
    {
        $this->chart = $chart;
        $this->setIdChart($chart->getId());
        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->chart;
    }


    /**
     * Set player
     *
     * @param Player $player
     * @return PlayerChart
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;
        $this->setIdPlayer($player->getIdPlayer());
        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set proof
     *
     * @param Proof $proof
     * @return PlayerChart
     */
    public function setProof(Proof $proof = null)
    {
        $this->proof = $proof;
        $this->setIdProof($proof->getIdProof());
        return $this;
    }

    /**
     * Get proof
     *
     * @return Proof
     */
    public function getProof()
    {
        return $this->proof;
    }


    /**
     * Set status
     *
     * @param PlayerChartStatus $status
     * @return PlayerChart
     */
    public function setStatus(PlayerChartStatus $status = null)
    {
        $this->status = $status;
        $this->setIdStatus($status->getIdStatus());
        return $this;
    }

    /**
     * Get status
     *
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preInsert()
    {
        $this->setPointChart(0);
        $this->setRank(10000);
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        if ($this->getRank() == 1) {
            $this->setTopScore(true);
        } else {
            $this->setTopScore(false);
        }
    }
}
