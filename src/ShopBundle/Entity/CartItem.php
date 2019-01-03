<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CartItem
 *
 * @ORM\Table(name="cart_items")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\CartItemRepository")
 */
class CartItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Quantity field can't be empty!")
     * @Assert\GreaterThan(0, message="Quantity can't be less than 1!")
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime", nullable=true)
     */
    private $dateAdded;

    /**
     * @var bool
     *
     * @ORM\Column(name="removedByUser", type="boolean")
     */
    private $removedByUser;

    /**
     * @var bool
     *
     * @ORM\Column(name="isOrdered", type="boolean")
     */
    private $isOrdered;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User", inversedBy="cartItems")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product", inversedBy="cartItems")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id", nullable=false)
     */
    private $product;

    /**
     * CartItem constructor.
     */
    public function __construct(User $user, Product $product)
    {
        $this->quantity = 1;
        $this->dateAdded = new \DateTime('now');
        $this->removedByUser = false;
        $this->isOrdered = false;
        $this->user = $user;
        $this->product = $product;
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded(): \DateTime
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTime $dateAdded
     */
    public function setDateAdded(\DateTime $dateAdded = null): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * Set removedByUser
     *
     * @param boolean $removedByUser
     *
     * @return CartItem
     */
    public function setRemovedByUser($removedByUser)
    {
        $this->removedByUser = $removedByUser;

        return $this;
    }

    /**
     * Get removedByUser
     *
     * @return bool
     */
    public function getRemovedByUser()
    {
        return $this->removedByUser;
    }

    /**
     * Set isOrdered
     *
     * @param boolean $isOrdered
     *
     * @return CartItem
     */
    public function setIsOrdered($isOrdered)
    {
        $this->isOrdered = $isOrdered;

        return $this;
    }

    /**
     * Get isOrdered
     *
     * @return bool
     */
    public function getIsOrdered()
    {
        return $this->isOrdered;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return float|int
     */
    public function calcProductAmount()
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }

    /**
     * @param User $currentUser
     * @return bool
     */
    public function isOwner(User $currentUser)
    {
        if ($currentUser === $this->getUser()) {
            if ($this->getRemovedByUser() === false && $this->getIsOrdered() === false) {
                return true;
            }
        }

        return false;
    }
}

