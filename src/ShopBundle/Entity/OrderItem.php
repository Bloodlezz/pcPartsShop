<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItem
 *
 * @ORM\Table(name="order_items")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\OrderItemRepository")
 */
class OrderItem
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
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product", inversedBy="orderItems")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="orderedAtPrice", type="decimal", precision=10, scale=2)
     */
    private $orderedAtPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var bool
     *
     * @ORM\Column(name="removedByUser", type="boolean")
     */
    private $removedByUser;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Order", inversedBy="orderItems")
     * @ORM\JoinColumn(name="orderId", referencedColumnName="id")
     */
    private $order;

    /**
     * OrderItem constructor.
     */
    public function __construct()
    {
        $this->removedByUser = false;
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderItem
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
     * Set orderedAtPrice
     *
     * @param string $orderedAtPrice
     *
     * @return OrderItem
     */
    public function setOrderedAtPrice($orderedAtPrice)
    {
        $this->orderedAtPrice = $orderedAtPrice;

        return $this;
    }

    /**
     * Get orderedAtPrice
     *
     * @return string
     */
    public function getOrderedAtPrice()
    {
        return $this->orderedAtPrice;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Set removedByUser
     *
     * @param boolean $removedByUser
     *
     * @return OrderItem
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
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }
}

