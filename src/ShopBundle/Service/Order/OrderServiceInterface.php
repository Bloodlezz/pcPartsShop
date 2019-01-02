<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 2.1.2019 г.
 * Time: 3:04
 */

namespace ShopBundle\Service\Order;


use ShopBundle\Entity\CartItem;

interface OrderServiceInterface
{
    /**
     * @param CartItem[] $cartItems
     * @param float $formTotal
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createOrder(array $cartItems, float $formTotal);

    /**
     * @param int $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOrder(int $orderId);
}