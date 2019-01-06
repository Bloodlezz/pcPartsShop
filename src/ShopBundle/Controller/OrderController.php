<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderItem;
use ShopBundle\Service\Cart\CartServiceInterface;
use ShopBundle\Service\Order\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends Controller
{
    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    /**
     * @var CartServiceInterface
     */
    private $cartService;

    /**
     * OrderController constructor.
     * @param CartServiceInterface $cartService
     */
    public function __construct(OrderServiceInterface $orderService, CartServiceInterface $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }


    /**
     * @Route("order/create", name="orderCreate")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        if ($request->getMethod() === "POST") {
            $formTotal = $request->request->get('total');
            $cartItems = $this->cartService->getUserCart();

            if ($this->orderService->createOrder($cartItems, $formTotal) === false) {
                $this->addFlash('message', 'Some product price was changed!');
                return $this->redirectToRoute('cart');
            }

            $this->addFlash('message', 'Order created successfully.');
        }

        return $this->redirectToRoute('myOrders');
    }

    /**
     * @Route("/myOrders", name="myOrders")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myOrdersAction()
    {
        /** @var Order[] $orders */
        $orders = $this->orderService->getUserOrdersByDateDesc();

        return $this->render('order/myOrders.html.twig', ['orders' => $orders]);
    }

    /**
     * @Route("order/view/{orderId}", name="orderView")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(int $orderId)
    {
        /** @var Order $currentOrder */
        $currentOrder = $this->orderService->viewOrder($orderId);

        if ($currentOrder) {
            /** @var OrderItem[] $orderItems */
            $orderItems = $currentOrder->getOrderItems();
            return $this->render('order/view.html.twig', ['orderItems' => $orderItems, 'order' => $currentOrder]);
        }

        $this->addFlash('notice', 'Order does not exists!');
        return $this->redirectToRoute('myOrders');
    }

    /**
     * @Route("order/updateStatus/{orderId}", name="updateStatus")
     * @param int $orderId
     */
    public function updateStatusAction(int $orderId)
    {
        if ($this->orderService->updateStatus($orderId) === false) {
            $this->addFlash('notice', 'Invalid status given!');
        }

        return $this->redirectToRoute('allOrders');
    }
}
