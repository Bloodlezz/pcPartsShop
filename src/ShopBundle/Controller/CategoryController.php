<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use ShopBundle\Service\Category\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * CategoryController constructor.
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }


    /**
     * @Route("/category/{categoryName}", name="categoryView")
     * @param string $categoryName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(string $categoryName)
    {
        $existsInDb = in_array($categoryName, $this->categoryService->getCategoriesName());

        if ($existsInDb) {
            /** @var Product[] $products */
            $products = $this->categoryService->getCategoryProducts($categoryName);

            return $this->render('category/view.html.twig', ['products' => $products]);
        }

        $this->addFlash('notice', 'Category does not exists!');
        return $this->redirectToRoute('homepage');
    }
}
