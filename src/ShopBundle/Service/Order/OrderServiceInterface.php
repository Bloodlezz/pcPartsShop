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
    public function getOrdersByDateDescending();

    /**
     * @param int $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOrder(int $orderId);
}