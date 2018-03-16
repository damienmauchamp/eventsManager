<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_user", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true, nullable=false)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=254, nullable=false)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=254, nullable=false)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=254, nullable=false, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     */
    private $role;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     */
    private $passwordChange;

    /// RELATIONS ENTRE ENTITÉS

    /**
     * Liste des évènements dont il participe
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="participants")
     *
     * @ORM\JoinTable(
     *  name="event_user",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id_user")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="event_id", referencedColumnName="id_event")
     *  }
     * )
     */
    private $events;

    /**
     * Liste des évènements qu'il a créé
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="createdBy")
     * @ORM\OrderBy({"createdDate" = "DESC"})
     */
    private $createdEvents;

    /**
     * Liste des commentaires qu'il a posté
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="postedBy")
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $postedComments;

    /**
     * Préfixe pour la réinitialisation de mot de passe
     * @var string prefixe
     */
    private $prefixe = "EVT_$";

    /// CONSTRUCTEUR

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->createdEvents = new ArrayCollection();
    }

    ///

    public function getRoles()
    {
        switch ($this->role) {
            case 0:
                return array('ROLE_USER');
            case 1:
                return array('ROLE_ADMIN');
        }
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->firstname,
            $this->lastname,
            $this->email
            // $this->salt
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->firstname,
            $this->lastname,
            $this->email
            // $this->salt
            ) = unserialize($serialized);
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @param UserPasswordEncoderInterface $encoder
     * @param int $length
     * @return string
     */
    public function reinitPassword($encoder, $length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $newPsw = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $newPsw .= mb_substr($chars, $index, 1);
        }

        $encoded = $encoder->encodePassword($this, $newPsw);
        $this->setPassword($encoded);
        $this->setPasswordChange(1);

        return $newPsw;
    }

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function changePassword($encoder) {
        $encoded = $encoder->encodePassword($this, $this->password);
        $this->setPassword($encoded);
        $this->setPasswordChange(0);
    }

    public function needsPasswordChange()
    {
        return $this->passwordChange;
    }

    public function needsPasswordChangeStr()
    {
        $length = strlen($this->prefixe);
        return (substr($this->password, 0, $length) === $this->prefixe);
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    /**
     * @param int $passwordChange
     */
    public function setPasswordChange($passwordChange): void
    {
        $this->passwordChange = $passwordChange;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function addEvent(Event $event)
    {
        $this->events[] = $event;
        return $this;
    }

    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * @return mixed
     */
    public function getCreatedEvents()
    {
        return $this->createdEvents;
    }

    /**
     * @return mixed
     */
    public function getPostedComments()
    {
        return $this->postedComments;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getRole() == 1;
    }


}
