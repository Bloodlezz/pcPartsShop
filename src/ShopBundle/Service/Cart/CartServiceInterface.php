<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 29.12.2018 г.
 * Time: 22:10
 */

namespace ShopBundle\Service\Cart;


use ShopBundle\Entity\CartItem;

interface CartServiceInterface
{
    /**
     * @param int $userId
     * @return CartItem[]|object|null
     */
    public function getUserCart(int $userId);

    /**
     * @param CartItem $cartItemToAdd
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToCart(CartItem $cartItemToAdd);

    /**
     * @param int $cartItemId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function increaseQty(int $cartItemId);

    /**
     * @param int $cartItemId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function decreaseQty(int $cartItemId);

    /**
     * @param int $cartItemId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFromCart(int $cartItemId);
}