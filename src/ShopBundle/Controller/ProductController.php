<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Product;
use ShopBundle\Form\ProductType;
use ShopBundle\Service\Category\CategoryServiceInterface;
use ShopBundle\Service\Product\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 * @Route("/product")
 */
class ProductController extends Controller
{
    const ALLOWED_FILE_TYPES = ['jpeg', 'png'];

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
     * @Route("/create", name="productCreate")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $categories = $this->categoryService->getAllCategories();

        /** @var Product $product */
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, ['validation_groups' => ['Default', 'Edit']]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->getData()->getImage();

            if ($form->isValid()) {
                $product->setUploader($this->getUser());
                $uploadDir = $this->getParameter('products_images');
                $this->productService->createProduct($product, $uploadDir, $imageFile);

                return $this->redirectToRoute("homepage");
            }

            return $this->render('product/create.html.twig',
                [
                    'form' => $form->createView(),
                    'categories' => $categories,
                    'product' => $product
                ]);
        }

        return $this->render('product/create.html.twig',
            [
                'categories' => $categories,
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/view/{id}", name="productView")
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

    /**
     * @Route("/edit/{id}", name="productEdit")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        /** @var Product $product */
        $product = $this->productService->getProductById($id);
        $currentImageName = $product->getImage();
        $categories = $this->categoryService->getAllCategories();
        $form = $this->createForm(ProductType::class, $product, ['validation_groups' => ['Default']]);
        $form->handleRequest($request);

//        if ($request->isMethod('POST')) {
//            $form->submit($request->request->get($form->getName()), false);
//        }

        if ($form->isSubmitted()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->getData()->getImage();

            if ($form->isValid()) {
                $uploadDir = $this->getParameter('products_images');
                $this->productService->editProduct($product, $uploadDir, $currentImageName, $imageFile);

                return $this->redirectToRoute('productView', ['id' => $id]);
            }

            return $this->render('product/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'categories' => $categories,
                    'product' => $product
                ]);
        }

        if ($product) {
            return $this->render('product/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'product' => $product,
                    'categories' => $categories
                ]
            );
        }

        $this->addFlash('message', 'Product does not exists!');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/delete/{id}", name="productDelete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(int $id)
    {
        /** @var Product $product */
        $product = $this->productService->getProductById($id);

        if ($product) {
            $uploadDir = $this->getParameter('products_images');
            $this->productService->deleteProduct($product, $uploadDir);

            $this->addFlash('message', 'Product deleted successfully.');
            return $this->redirectToRoute('homepage');
        }

        $this->addFlash('message', 'Product does not exists!');
        return $this->redirectToRoute('homepage');
    }
}
