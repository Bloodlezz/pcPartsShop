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
use ShopBundle\Service\User\UserServiceInterface;
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
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var User
     */
    private $currentUser;

    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     * @param RequestStack $request
     * @param TokenStorageInterface $security
     * @param UserServiceInterface $userService
     */
    public function __construct(OrderRepository $orderRepository,
                                RequestStack $request,
                                TokenStorageInterface $security,
                                UserServiceInterface $userService)
    {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->security = $security;
        $this->userService = $userService;
        $this->currentUser = $security->getToken()->getUser();
    }


    /**
     * @param CartItem[] $cartItems
     * @param $formTotal
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return boolean
     */
    public function createOrder(array $cartItems, $formTotal)
    {
        $orderTotal = $this->getCartTotal($cartItems);

        if (abs($orderTotal - $formTotal) < 0.000001) {

            $order = new Order($this->currentUser);

            /** @var CartItem $cartItem */
            foreach ($cartItems as $cartItem) {
                $currentAmount = $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
                $currentOrderItem = new OrderItem();
                $currentOrderItem->setProduct($cartItem->getProduct());
                $currentOrderItem->setQuantity($cartItem->getQuantity());
                $currentOrderItem->setAmount($currentAmount);
                $cartItem->setIsOrdered(true);
                $order->addOrderItem($currentOrderItem);
            }

            $order->setTotal($orderTotal);
            $this->orderRepository->create($order);
            // SET ALL CART ITEMS IN CART AS ORDERED
            $this->userService->edit($this->currentUser);
            return true;
        }

        return false;
    }

    /**
     * @return ArrayCollection|Order[]
     */
    public function getOrdersByDateDescending()
    {
        return $this->orderRepository
            ->findBy(
                ['user' => $this->currentUser->getId()],
                ['created' => 'DESC']
            );
    }

    /**
     * @param int $orderId
     * @return Order|object
     */
    public function viewOrder(int $orderId)
    {
        /** @var Order $currentOrder */
        $currentOrder = $this->orderRepository->find($orderId);

        if ($currentOrder->isOwner($this->currentUser)) {
            return $currentOrder;
        }
    }

    /**
     * @param ArrayCollection|CartItem[] $cartItems
     */
    private function getCartTotal(array $cartItems)
    {
        $cartTotal = 0;

        /** @var CartItem $cartItem */
        foreach ($cartItems as $cartItem) {
            $currentAmount = $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
            $cartTotal += $currentAmount;
        }

        return $cartTotal;
    }
}