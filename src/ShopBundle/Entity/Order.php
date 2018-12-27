<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\OrderRepository")
 */
class Order
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var float
     *
     * @ORM\Column(name="totalPrice", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="statusUpdate", type="datetime", nullable=true)
     */
    private $statusUpdate;

    /**
     * @var ArrayCollection|OrderItem[]
     *
     *@ORM\OneToMany(targetEntity="ShopBundle\Entity\OrderItem", mappedBy="order")
     */
    private $orderItems;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->totalPrice = 0;
        $this->status = "open";
        $this->orderItems = new ArrayCollection();
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Order
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statusUpdate
     *
     * @param \DateTime $statusUpdate
     *
     * @return Order
     */
    public function setStatusUpdate($statusUpdate)
    {
        $this->statusUpdate = $statusUpdate;

        return $this;
    }

    /**
     * Get statusUpdate
     *
     * @return \DateTime
     */
    public function getStatusUpdate()
    {
        return $this->statusUpdate;
    }

    /**
     * @return ArrayCollection|OrderItem[]
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem $orderItem
     *
     * @return Order
     */
    public function addOrderItem($orderItem): Order
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }
}

