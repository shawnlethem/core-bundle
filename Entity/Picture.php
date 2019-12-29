<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Proof
 *
 * @ORM\Table(name="vgr_picture", indexes={@ORM\Index(name="idxIdPicture", columns={"idPicture"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PictureRepository")
 */
class Picture
{
    use \VideoGamesRecords\CoreBundle\Model\Player;
    use \VideoGamesRecords\CoreBundle\Model\Game;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", nullable=false)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(name="metadata", type="text", nullable=true)
     */
    private $metadata;


    public function __construct()
    {

    }


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Picture [%s]', $this->id);
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set metadata
     *
     * @param string $metadata
     * @return $this
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Get metadata
     *
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
