<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 29.12.2018 г.
 * Time: 22:14
 */

namespace ShopBundle\Service\Cart;


use ShopBundle\Entity\CartItem;
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
        $currentUser = $cartItemToAdd->getUser();
        $currentProduct = $cartItemToAdd->getProduct();

        /** @var CartItem $foundInUserCart */
        $foundInUserCart = $this->cartItemRepository->findOneBy(
            [
                'user' => $currentUser->getId(),
                'product' => $currentProduct->getId(),
                'isOrdered' => false
            ]
        );

        if ($foundInUserCart) {
            if ($foundInUserCart->getRemovedByUser()) {
                $foundInUserCart->setRemovedByUser(false);
                $foundInUserCart->setQuantity(1);
            } else {
                $foundInUserCart->setQuantity($foundInUserCart->getQuantity() + 1);
            }

            return $this->cartItemRepository->editCartItem($foundInUserCart);
        }

        return $this->cartItemRepository->addToCart($cartItemToAdd);
    }

    /**
     * @param int $cartItemId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function increaseQty(int $cartItemId)
    {
        /** @var CartItem $cartItem */
        $cartItem = $this->cartItemRepository->find($cartItemId);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);

            return $this->cartItemRepository->editCartItem($cartItem);
        }
    }

    /**
     * @param int $cartItemId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function decreaseQty(int $cartItemId)
    {
        /** @var CartItem $cartItem */
        $cartItem = $this->cartItemRepository->find($cartItemId);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() - 1);

            return $this->cartItemRepository->editCartItem($cartItem);
        }
    }

    /**
     * @param int $cartItemId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFromCart(int $cartItemId)
    {
        /** @var CartItem $cartItem */
        $cartItem = $this->cartItemRepository->find($cartItemId);

        if ($cartItem) {
            $cartItem->setRemovedByUser(true);

            return $this->cartItemRepository->editCartItem($cartItem);
        }
    }
}