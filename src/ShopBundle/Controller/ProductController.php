<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use ShopBundle\Service\Category\CategoryServiceInterface;
use ShopBundle\Service\Product\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 */
class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface $productService
     */
    private $productService;

    /**
     * @var CategoryServiceInterface $categoryService
     */
    private $categoryService;

    /**
     * ProductController constructor.
     * @param ProductServiceInterface $productService
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(ProductServiceInterface $productService, CategoryServiceInterface $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }


    /**
     * @Route("/product/view/{id}", name="productView")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(int $id)
    {
        /** @var Product $currentProduct */
        $currentProduct = $this->productService->getProductById($id);

        if ($currentProduct) {
            return $this->render('product/view.html.twig', ['product' => $currentProduct]);
        }

        $this->addFlash('message', 'Product does not exists!');
        return $this->redirectToRoute('homepage');
    }
}
