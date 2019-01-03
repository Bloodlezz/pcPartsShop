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
     * @return CartItem[]|object|null
     */
    public function getUserCart();

    /**
     * @param CartItem $cartItemToAdd
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToCart(CartItem $cartItemToAdd);

    /**
     * @param int $cartItemId
     * @return boolean
     */
    public function increaseQty(int $cartItemId);

    /**
     * @param int $cartItemId
     * @return boolean
     */
    public function decreaseQty(int $cartItemId);

    /**
     * @param int $cartItemId
     * @return boolean
     */
    public function removeFromCart(int $cartItemId);
}