<?php

namespace ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\StockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Stock
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"stock", "product", "access", "user"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"stock", "product", "access"})
     * @Assert\NotBlank(message="Value not be blank")
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     * @Groups({"stock", "product"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     * @Groups({"stock", "product"})
     * @Assert\DateTime()
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="ApiBundle\Entity\Product", mappedBy="stock", orphanRemoval=true)
     * @Groups({"stock"})
     * @var Products[]
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="StockAccess", mappedBy="stock", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"stock", "product"})
     * @var StockAccesses[]
     * @Assert\Valid()
     */
    private $stockAccesses;

    public function __construct()
    {
        $this->stockAccesses = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Stock
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
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
     * @return Stock
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

    /**
     * Add product
     *
     * @param Product $product
     *
     * @return Stock
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        $this->products->removeElement($product);
    }


    /**
     * Add StockAccess
     *
     * @param StockAccess $stockAccess
     *
     * @return Stock
     */
    public function addStockAccess(StockAccess $stockAccess)
    {
        $stockAccess->setStock($this);

        if (!$this->stockAccesses->contains($stockAccess)){
            $this->stockAccesses->add($stockAccess);
        }

        return $this;
    }

    /**
     * Remove StockAccesses
     *
     * @param StockAccess $stockAccess
     */
    public function removeStockAccess(StockAccess $stockAccess)
    {
        $this->stockAccesses->removeElement($stockAccess);
    }

    /**
     * Get StockAccesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStockAccesses()
    {
        return $this->stockAccesses;
    }
}
