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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @var SessionInterface
     */
    private $session;

    /**
     * CartService constructor.
     * @param CartItemRepository $cartItemRepository
     * @param TokenStorageInterface $security
     * @param SessionInterface $session
     */
    public function __construct(CartItemRepository $cartItemRepository,
                                TokenStorageInterface $security,
                                SessionInterface $session)
    {
        $this->cartItemRepository = $cartItemRepository;
        $this->security = $security;
        $this->session = $session;
    }


    /**
     * @return User|object|null
     */
    public function getCurrentUser()
    {
        return $this->security->getToken()->getUser();
    }

    /**
     * @return ArrayCollection|CartItem[]|null
     */
    public function getUserCart()
    {
        return $this->cartItemRepository->findBy(
            [
                'user' => $this->getCurrentUser()->getId(),
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
                'user' => $this->getCurrentUser()->getId(),
                'product' => $currentProduct->getId(),
                'isOrdered' => false
            ]
        );

        $this->session->set('cartCount', $this->session->get('cartCount') + 1);

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
            if ($cartItem->isOwner($this->getCurrentUser())) {
                $cartItem->setQuantity($cartItem->getQuantity() + 1);
                $this->session->set('cartCount', $this->session->get('cartCount') + 1);

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
            if ($cartItem->isOwner($this->getCurrentUser())) {
                if (1 < $cartItem->getQuantity()) {
                    $cartItem->setQuantity($cartItem->getQuantity() - 1);
                    $this->session->set('cartCount', $this->session->get('cartCount') - 1);

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
            if ($cartItem->isOwner($this->getCurrentUser())) {
                $cartItem->setRemovedByUser(true);
                $cartItem->setDateAdded(null);
                $this->session->set('cartCount', $this->session->get('cartCount') - $cartItem->getQuantity());
                $this->cartItemRepository->editCartItem($cartItem);
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getCartCount()
    {
        if ($this->session->has('cartCount')) {
            return $this->session->get('cartCount');
        }

        $result = 0;
        $userCartItems = $this->getUserCart();

        /** @var CartItem $cartItem */
        foreach ($userCartItems as $cartItem) {
            $result += $cartItem->getQuantity();
        }

        $this->session->set('cartCount', $result);

        return $result;
    }
}