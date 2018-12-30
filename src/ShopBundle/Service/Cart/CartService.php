<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 29.12.2018 г.
 * Time: 22:14
 */

namespace ShopBundle\Service\Cart;


use ShopBundle\Entity\CartItem;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\CartItemRepository;

class CartService implements CartServiceInterface
{
    /**
     * @var CartItemRepository
     */
    private $cartItemRepository;

    /**
     * CartService constructor.
     * @param CartItemRepository $cartItemRepository
     */
    public function __construct(CartItemRepository $cartItemRepository)
    {
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * @param int $userId
     * @return CartItem[]|object|null
     */
    public function getUserCart(int $userId)
    {
        return $this->cartItemRepository->findBy(
            [
                'user' => $userId,
                'removedByUser' => false,
                'isOrdered' => false
            ]
        );
    }

    /**
     * @param CartItem $cartItemToAdd
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToCart(CartItem $cartItemToAdd)
    {
        $userCartItems = $cartItemToAdd->getUser()->getCartItems();
        foreach ($userCartItems as $cartItem) {
            if ($cartItem->getProduct()->getId() === $cartItemToAdd->getProduct()->getId()) {
                if ($cartItem->getRemovedByUser() === true) {
                    $cartItem->setRemovedByUser(false);
                    $cartItem->setQuantity(1);
                } else if ($cartItem->getIsOrdered() === false) {
                    $cartItem->setQuantity($cartItem->getQuantity() + 1);
                }

                return $this->cartItemRepository->editCartItem($cartItem);
            }
        }

        return $this->cartItemRepository->addToCart($cartItemToAdd);
    }
}