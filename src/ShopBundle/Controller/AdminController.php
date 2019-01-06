<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Order;
use ShopBundle\Service\Order\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
