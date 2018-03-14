<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * access
 *
 * @ORM\Table(name="access")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\AccessRepository")
 */
class Access
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"access", "stock", "user"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="role", type="integer")
     * @Groups({"access", "stock", "user"})
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 4,
     *      invalidMessage = "Invalid role"
     * )
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Stock", inversedBy="access", cascade={"persist"})
     * @ORM\JoinColumn(name="stock_id", referencedColumnName="id")
     * @Groups({"access", "user"})
     * @Assert\NotBlank()
     */
    private $stock;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\User", inversedBy="access")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups({"access", "stock"})
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param mixed $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * Set role
     *
     * @param integer $role
     *
     * @return access
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }
}

