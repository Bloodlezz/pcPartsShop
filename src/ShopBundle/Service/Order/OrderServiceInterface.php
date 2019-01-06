<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 2.1.2019 г.
 * Time: 3:04
 */

namespace ShopBundle\Service\Order;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\CartItem;
use ShopBundle\Entity\Order;

interface OrderServiceInterface
{
    /**
     * @param CartItem[] $cartItems
     * @param $formTotal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createOrder(array $cartItems, $formTotal);

    /**
     * @return ArrayCollection|Order[]
     */
    public function getUserOrdersByDateDesc();

    /**
     * @param int $orderId
     * @return Order|object
     */
    public function viewOrder(int $orderId);

    /**
     * @return ArrayCollection|Order[]
     */
    public function getOrdersByDateDesc();

    /**
     * @param int $orderId
     * @return bool
     */
    public function updateStatus(int $orderId);

    /**
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateOrderComment(Order $order);
}