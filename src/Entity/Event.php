<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

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
    private $date;

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
        $this->participants = new ArrayCollection();
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = new \DateTime($date);
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


}
