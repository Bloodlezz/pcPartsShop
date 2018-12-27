<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class OrderItemController extends Controller
{
    /**
     * @Route("/cart/add/{id}", name="cartAdd")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToCartAction(int $id)
    {
        $order = new Order();
        $order->setUser($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('productView', ['id' => $id]);
    }
}
