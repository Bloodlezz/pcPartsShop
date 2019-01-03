<?php
/**
 * Created by PhpStorm.
 * User: Bloodless
 * Date: 29.12.2018 г.
 * Time: 22:14
 */

namespace ShopBundle\Service\Cart;


use Doctrine\Common\Collections\ArrayCollection;
use ShopBundle\Entity\CartItem;
use ShopBundle\Entity\User;
use ShopBundle\Repository\CartItemRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CartService implements CartServiceInterface
{
    /**
     * @var CartItemRepository
     */
    private $cartItemRepository;

    /**
     * @var TokenStorageInterface
     */
    private $security;

    /**
     * @var User
     */
    private $currentUser;

    /**
     * CartService constructor.
     * @param CartItemRepository $cartItemRepository
     * @param TokenStorageInterface $security
     */
    public function __construct(CartItemRepository $cartItemRepository, TokenStorageInterface $security)
    {
        $this->cartItemRepository = $cartItemRepository;
        $this->security = $security;
        $this->currentUser = $security->getToken()->getUser();
    }

    /**
     * @return ArrayCollection|CartItem[]|null
     */
    public function getUserCart()
    {
        return $this->cartItemRepository->findBy(
            [
                'user' => $this->currentUser->getId(),
                'removedByUser' => false,
                'isOrdered' => false
            ],
            [
                'dateAdded' => 'DESC'
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
        $currentProduct = $cartItemToAdd->getProduct();

        /** @var CartItem $foundInUserCart */
        $foundInUserCart = $this->cartItemRepository->findOneBy(
            [
                'user' => $this->currentUser->getId(),
                'product' => $currentProduct->getId(),
                'isOrdered' => false
            ]
        );

        if ($foundInUserCart) {
            if ($foundInUserCart->getRemovedByUser()) {
                $foundInUserCart->setRemovedByUser(false);
                $foundInUserCart->setQuantity(1);
                $foundInUserCart->setDateAdded(new \DateTime('now'));
            } else {
                $foundInUserCart->setQuantity($foundInUserCart->getQuantity() + 1);
            }

            return $this->cartItemRepository->editCartItem($foundInUserCart);
        }

        return $this->cartItemRepository->addCartItem($cartItemToAdd);
    }

    /**
     * @param int $cartItemId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return boolean
     */
    public function increaseQty(int $cartItemId)
    {
        /** @var CartItem $cartItem */
        $cartItem = $this->cartItemRepository->find($cartItemId);

        if ($cartItem) {
            if ($cartItem->isOwner($this->currentUser)) {
                $cartItem->setQuantity($cartItem->getQuantity() + 1);

                $this->cartItemRepository->editCartItem($cartItem);
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $cartItemId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return boolean
     */
    public function decreaseQty(int $cartItemId)
    {
        /** @var CartItem $cartItem */
        $cartItem = $this->cartItemRepository->find($cartItemId);

        if ($cartItem) {
            if ($cartItem->isOwner($this->currentUser)) {
                if (1 < $cartItem->getQuantity()) {
                    $cartItem->setQuantity($cartItem->getQuantity() - 1);

                    $this->cartItemRepository->editCartItem($cartItem);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param int $cartItemId
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return boolean
     */
    public function removeFromCart(int $cartItemId)
    {
        /** @var CartItem $cartItem */
        $cartItem = $this->cartItemRepository->find($cartItemId);

        if ($cartItem) {
            if ($cartItem->isOwner($this->currentUser)) {
                $cartItem->setRemovedByUser(true);
                $cartItem->setDateAdded(null);
                $this->cartItemRepository->editCartItem($cartItem);
                return true;
            }
        }

        return false;
    }
}