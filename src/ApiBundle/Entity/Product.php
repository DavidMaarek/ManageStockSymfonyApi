<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"home", "histories"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"home", "histories"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)

     * @Assert\NotBlank()
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="integer", length=255)
     * @Groups({"home"})
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)

     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="picture1", type="string", length=255, nullable=true)

     * @Assert\Image(mimeTypes = {"image/jpg", "image/jpeg", "image/png"})
     */
    private $picture1;

    /**
     * @var string
     *
     * @ORM\Column(name="picture2", type="string", length=255, nullable=true)

     * @Assert\Image(mimeTypes = {"image/jpg", "image/jpeg", "image/png"})
     */
    private $picture2;

    /**
     * @var string
     *
     * @ORM\Column(name="picture3", type="string", length=255, nullable=true)

     * @Assert\Image(mimeTypes = {"image/jpg", "image/jpeg", "image/png"})
     */
    private $picture3;

    /**
     * @var string
     *
     * @ORM\Column(name="picture4", type="string", length=255, nullable=true)

     * @Assert\Image(mimeTypes = {"image/jpg", "image/jpeg", "image/png"})
     */
    private $picture4;

    /**
     * @var string
     *
     * @ORM\Column(name="picture5", type="string", length=255, nullable=true)

     * @Assert\Image(mimeTypes = {"image/jpg", "image/jpeg", "image/png"})
     */
    private $picture5;

    /**
     * @var string
     *
     * @ORM\Column(name="pdf", type="string", length=255, nullable=true)

     * @Assert\File(mimeTypes = {"application/pdf", "application/x-pdf"})
     */
    private $pdf;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=255, nullable=true)

     * Assert\File(mimeTypes = {"application/zip"})
     */
    private $zip;

    /**
     * @return string
     */
    public function getPicture1()
    {
        return $this->picture1;
    }

    /**
     * @param string $picture1
     */
    public function setPicture1($picture1)
    {
        $this->picture1 = $picture1;
    }

    /**
     * @return string
     */
    public function getPicture2()
    {
        return $this->picture2;
    }

    /**
     * @param string $picture2
     */
    public function setPicture2($picture2)
    {
        $this->picture2 = $picture2;
    }

    /**
     * @return string
     */
    public function getPicture3()
    {
        return $this->picture3;
    }

    /**
     * @param string $picture3
     */
    public function setPicture3($picture3)
    {
        $this->picture3 = $picture3;
    }

    /**
     * @return string
     */
    public function getPicture4()
    {
        return $this->picture4;
    }

    /**
     * @param string $picture4
     */
    public function setPicture4($picture4)
    {
        $this->picture4 = $picture4;
    }

    /**
     * @return string
     */
    public function getPicture5()
    {
        return $this->picture5;
    }

    /**
     * @param string $picture5
     */
    public function setPicture5($picture5)
    {
        $this->picture5 = $picture5;
    }

    /**
     * @return string
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * @param string $pdf
     */
    public function setPdf($pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)

     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)

     * @Assert\DateTime()
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="ApiBundle\Entity\Stock", inversedBy="products")
     * @ORM\JoinColumn(name="stock_id", referencedColumnName="id")
     * @Groups({"histories"})
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity="ApiBundle\Entity\History", mappedBy="product", cascade={"remove"})

     */
    private $history;

    /**
     * @return mixed
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param mixed $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }
    
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
     * @return Product
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
     * Set reference
     *
     * @param string $reference
     *
     * @return Product
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     *
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Product
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Product
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime());
        }

        if (!$this->getUpdatedAt()) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}

