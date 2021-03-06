<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Event;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LabelRepository")
 */
class Label
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_label", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;


    /// RELATIONS ENTRE ENTITÉS


    /**
     * Liste des évènements sur lequels on retrouve la catégorie
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="labels")
     *
     * @ORM\JoinTable(
     *  name="event_label",
     *  joinColumns={
     *      @ORM\JoinColumn(name="label_id", referencedColumnName="id_label")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="event_id", referencedColumnName="id_event")
     *  }
     * )
     */
    private $events;

    /**
     * @param \App\Entity\Event $event
     */
    public function setEvent(Event $event): void
    {
        $this->events[] = $event;
    }

    public function getEvent(Event $event) {
        return $this->events->get($event->getId());
    }

    public function removeEvent(Event $event) {
        $this->events->removeElement($event);
    }

    /// CONSTRUCTEUR
    ///
    /**
     * Label constructor.
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
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
    public function getEvents()
    {
        return $this->events;
    }
}
