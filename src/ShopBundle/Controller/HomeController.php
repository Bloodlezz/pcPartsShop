<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var Product[] $rr */
        $rr = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('default/index.html.twig', ['prod' => $rr]);
    }
}
