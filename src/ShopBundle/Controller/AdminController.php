<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderItem;
use ShopBundle\Service\Order\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package ShopBundle\Controller
 * @Route("/administration")
 */
class AdminController extends Controller
{
    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    /**
     * AdminController constructor.
     * @param OrderServiceInterface $orderService
     */
    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }


    /**
     * @Route("/orders", name="allOrders")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allOrdersAction()
    {
        /** @var Order[] $orders */
        $orders = $this->orderService->getOrdersByDateDesc();

        return $this->render('administration/orders.html.twig', ['orders' => $orders]);
    }

    /**
     * @Route("/orders/order/{orderId}", name="singleOrder")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param int $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function singleOrderAction(int $orderId, Request $request)
    {
        /** @var Order $currentOrder */
        $currentOrder = $this->orderService->viewOrder($orderId);

        if ($currentOrder) {
            if ($request->getMethod() === "POST") {
                $comment = $request->request->get('comment');
                $currentOrder->setComment($comment);
                $this->orderService->updateOrderComment($currentOrder);

                $this->addFlash('message', 'Comment updated.');
                return $this->redirectToRoute('singleOrder', ['orderId' => $currentOrder->getId()]);
            }

            /** @var OrderItem[] $orderItems */
            $orderItems = $currentOrder->getOrderItems();
            return $this->render('administration/singleOrder.html.twig', ['orderItems' => $orderItems, 'order' => $currentOrder]);
        }

        $this->addFlash('notice', 'Order does not exists!');
        return $this->redirectToRoute('allOrders');
    }
}
