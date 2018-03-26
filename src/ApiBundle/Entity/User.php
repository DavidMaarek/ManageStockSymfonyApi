<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"user", "access", "stock", "auth-token"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"user", "access", "auth-token"})
     * @Assert\NotBlank(message="Vous devez renseigner un nom")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Groups({"user", "access", "auth-token"})
     * @Assert\NotBlank(message="Vous devez renseigner un prénom")
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Groups({"user", "access", "auth-token"})
     * @Assert\NotBlank(message="Vous devez renseigner un email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Vous devez renseigner un mot de passe")
     * @Assert\Length(min="4")
     */
    private $plainPassword;

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="society", type="string", length=255, nullable=true)
     * @Groups({"user", "access"})
     */
    private $society;

    /**
     * @ORM\OneToMany(targetEntity="StockAccess", mappedBy="user", cascade={"remove"})
     * @Groups({"user"})
     */
    private $access;


    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param mixed $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set society
     *
     * @param string $society
     *
     * @return User
     */
    public function setSociety($society)
    {
        $this->society = $society;

        return $this;
    }

    /**
     * Get society
     *
     * @return string
     */
    public function getSociety()
    {
        return $this->society;
    }

    public function getRoles()
    {
        return [];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Suppression des données sensibles
        $this->plainPassword = null;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->access = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add access
     *
     * @param \ApiBundle\Entity\StockAccess $access
     *
     * @return User
     */
    public function addAccess(\ApiBundle\Entity\StockAccess $access)
    {
        $this->access[] = $access;

        return $this;
    }

    /**
     * Remove access
     *
     * @param \ApiBundle\Entity\StockAccess $access
     */
    public function removeAccess(\ApiBundle\Entity\StockAccess $access)
    {
        $this->access->removeElement($access);
    }
}
