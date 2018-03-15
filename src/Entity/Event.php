<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_event", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $place;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="string", length=3000)
     */
    private $description;

    /// RELATIONS ENTRE ENTITÉS

    /**
     * ID du créateur de l'évènement
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="createdEvents")
     * @ORM\JoinColumn(name="id_creator", referencedColumnName="id_user")
     */
    private $createdBy;

    /**
     * Liste catégories associées à l'évènement
     * @ORM\ManyToMany(targetEntity="App\Entity\Label", mappedBy="events")
     *
     * @ORM\JoinTable(
     *  name="event_label",
     *  joinColumns={
     *      @ORM\JoinColumn(name="event_id", referencedColumnName="id_event")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="label_id", referencedColumnName="id_label")
     *  }
     * )
     */
    private $labels;

    /**
     * Liste des commentaires sur l'évènement
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="event")
     */
    private $comments;

    /**
     * Liste des participants à cette évènement
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="events")
     *
     * @ORM\JoinTable(
     *  name="event_user",
     *  joinColumns={
     *      @ORM\JoinColumn(name="event_id", referencedColumnName="id_event")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id_user")
     *  }
     * )
     */
    private $participants;


    /// CONSTRUCTEUR

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->dateDebut = new \DateTime();
        $this->dateFin = new \DateTime("+1 day");
        $this->createdDate = new \DateTime();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     */
    public function setPlace($place): void
    {
        $this->place = $place;
    }

    /**
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param mixed $dateDebut
     */
    public function setDateDebut($dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateFin
     */
    public function setDateFin($dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate): void
    {
        $this->createdDate = new \DateTime($createdDate);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function addLabel(Label $label)
    {
        $this->labels[] = $label;
        return $this;
    }

    public function removeLabel(Label $label) {
        $this->labels->removeElement($label);
    }

    /**
     * @param mixed $labels
     */
    public function setLabels($labels): void
    {
        $this->labels = new ArrayCollection($labels);
    }

    /**
     * @return mixed
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }


    /// Fonctions

    /**
     * @param $user
     * @return bool
     */
    public function isCreator($user = null)
    {
        return $user && $user == $this->getCreatedBy();
//        return $user && $user->getId() == $this->getCreatedBy();
    }
}
