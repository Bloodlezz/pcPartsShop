<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 2.1.2019 г.
 * Time: 3:05
 */

namespace ShopBundle\Service\Order;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\CartItem;
use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderItem;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderService implements OrderServiceInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var TokenStorageInterface
     */
    private $security;

    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository, RequestStack $request, TokenStorageInterface $security)
    {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->security = $security;
    }


    /**
     * @param CartItem[] $cartItems
     * @param float $formTotal
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return boolean
     */
    public function createOrder(array $cartItems, float $formTotal)
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getToken()->getUser();
        $order = new Order($currentUser);
        $orderTotal = 0;

        /** @var CartItem $cartItem */
        foreach ($cartItems as $cartItem) {
            $currentAmount = $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
            $orderTotal += $currentAmount;
            $currentOrderItem = new OrderItem();
            $currentOrderItem->setProduct($cartItem->getProduct());
            $currentOrderItem->setQuantity($cartItem->getQuantity());
            $currentOrderItem->setAmount($currentAmount);
            $order->addOrderItem($currentOrderItem);
        }

        if ($orderTotal !== $formTotal) {
            return false;
        }

        $order->setTotal($orderTotal);
        $this->orderRepository->create($order);
        return true;
    }

    /**
     * @param int $orderId
     * @return Order|object
     */
    public function viewOrder(int $orderId)
    {
        return $this->orderRepository->find($orderId);
    }
}