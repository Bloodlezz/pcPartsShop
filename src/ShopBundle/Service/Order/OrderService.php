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
use ShopBundle\Service\Cart\CartServiceInterface;
use ShopBundle\Service\User\UserServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderService implements OrderServiceInterface
{
    const VALID_STATUS = ["processing", "sent", "canceled"];

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
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CartServiceInterface
     */
    private $cartService;

    /**
     * OrderService constructor.
     * @param OrderRepository $orderRepository
     * @param RequestStack $request
     * @param TokenStorageInterface $security
     * @param UserServiceInterface $userService
     * @param SessionInterface $session
     * @param CartServiceInterface $cartService
     */
    public function __construct(OrderRepository $orderRepository,
                                RequestStack $request,
                                TokenStorageInterface $security,
                                UserServiceInterface $userService,
                                SessionInterface $session,
                                CartServiceInterface $cartService)
    {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->security = $security;
        $this->userService = $userService;
        $this->currentUser = $security->getToken()->getUser();
        $this->session = $session;
        $this->cartService = $cartService;
    }


    /**
     * @param CartItem[] $cartItems
     * @param $formTotal
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return boolean
     */
    public function createOrder(array $cartItems, $formTotal)
    {
        $orderTotal = $this->getOrderTotal($cartItems);

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
                $productOrderedCount = $cartItem->getProduct()->getOrderedCount();
                $cartItem->getProduct()->setOrderedCount($productOrderedCount + $cartItem->getQuantity());
                $order->addOrderItem($currentOrderItem);
            }

            $order->setTotal($orderTotal);
            $this->orderRepository->create($order);
            // SET ALL CART ITEMS IN CART AS ORDERED
            $this->userService->edit($this->currentUser);
            $this->session->set($this->cartService->getCartNameForSession(), 0);
            return true;
        }

        return false;
    }

    /**
     * @return ArrayCollection|Order[]
     */
    public function getUserOrdersByDateDesc()
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
    private function getOrderTotal(array $cartItems)
    {
        $cartTotal = 0;

        /** @var CartItem $cartItem */
        foreach ($cartItems as $cartItem) {
            $currentAmount = $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
            $cartTotal += $currentAmount;
        }

        return $cartTotal;
    }

    /**
     * @return ArrayCollection|Order[]
     */
    public function getOrdersByDateDesc()
    {
        return $this->orderRepository->findBy([], ['created' => 'DESC', 'id' => 'DESC']);
    }

    /**
     * @param int $orderId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return bool
     */
    public function updateStatus(int $orderId)
    {
        $newStatus = $this->request->getCurrentRequest()->get('status');

        if (in_array($newStatus, self::VALID_STATUS) === false) {
            return false;
        }

        /** @var Order $order */
        $order = $this->orderRepository->find($orderId);
        $order->setStatus($newStatus);
        $order->setStatusUpdate(new \DateTime('now'));
        $this->orderRepository->update($order);

        return true;
    }

    /**
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateOrderComment(Order $order)
    {
        return $this->orderRepository->update($order);
    }
}