<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_comment", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $content;


    /// RELATIONS ENTRE ENTITÉS

    /**
     * ID du créateur du commentaire
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="postedComments")
     * @ORM\JoinColumn(name="id_poster", referencedColumnName="id_user")
     */
    private $postedBy;

    /**
     * ID de l'évènement sur lequel on retrouve le commentaire
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="comments")
     * @ORM\JoinColumn(name="id_event", referencedColumnName="id_event")
     */
    private $event;


    /// CONSTRUCTEUR

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /// GETTEURS / SETTEURS

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getPostedBy()
    {
        return $this->postedBy;
    }

    /**
     * @param mixed $postedBy
     */
    public function setPostedBy($postedBy): void
    {
        $this->postedBy = $postedBy;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }
}
