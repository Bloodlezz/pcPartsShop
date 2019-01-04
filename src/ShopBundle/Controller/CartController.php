<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\CartItem;
use ShopBundle\Entity\Product;
use ShopBundle\Service\Cart\CartServiceInterface;
use ShopBundle\Service\Product\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CartController
 * @package ShopBundle\Controller
 * @Route("/cart")
 */
class CartController extends Controller
{
    /**
     * @var CartServiceInterface
     */
    private $cartService;

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * CartController constructor.
     * @param CartServiceInterface $cartService
     * @param ProductServiceInterface $productService
     */
    public function __construct(CartServiceInterface $cartService, ProductServiceInterface $productService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }


    /**
     * @Route("/add/{productId}", name="addToCart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $productId
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(int $productId)
    {
        $currentProduct = $this->productService->getProductById($productId);

        if ($currentProduct) {
            /** @var CartItem $cartItemToAdd */
            $cartItemToAdd = new CartItem($this->getUser(), $currentProduct);
            $this->cartService->addToCart($cartItemToAdd);

            $this->addFlash('message', 'Product added to cart.');
            return $this->redirectToRoute('productView', ['id' => $productId]);
        }

        $this->addFlash('notice', 'Product does not exists!');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/", name="cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cartAction()
    {
        /** @var CartItem[] $cartItems */
        $cartItems = $this->cartService->getUserCart();

        return $this->render('cart/cart.html.twig', ['cartItems' => $cartItems]);
    }

    /**
     * @Route("/inc/{cartItemId}", name="increaseQty")
     * @param int $cartItemId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function increaseItemQtyAction(int $cartItemId)
    {
        $this->cartService->increaseQty($cartItemId);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/dec/{cartItemId}", name="decreaseQty")
     * @param int $cartItemId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function decreaseItemQtyAction(int $cartItemId)
    {
        $this->cartService->decreaseQty($cartItemId);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/remove/{cartItemId}", name="removeFromCart")
     * @param int $cartItemId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFromCartAction(int $cartItemId)
    {
        if ($this->cartService->removeFromCart($cartItemId)) {
            $this->addFlash('message', 'Product removed successfully.');
        } else {
            $this->addFlash('notice', 'Product can\'t be removed! Please try again.');
        }

        return $this->redirectToRoute('cart');
    }
}
