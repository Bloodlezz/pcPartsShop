<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderItem;
use ShopBundle\Entity\Product;
use ShopBundle\Form\OrderType;
use ShopBundle\Form\ProductType;
use ShopBundle\Service\Category\CategoryServiceInterface;
use ShopBundle\Service\Order\OrderServiceInterface;
use ShopBundle\Service\Product\ProductServiceInterface;
use ShopBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package ShopBundle\Controller
 * @Route("/administration")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var CategoryServiceInterface
     */

    private $categoryService;
    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * AdminController constructor.
     * @param OrderServiceInterface $orderService
     */
    public function __construct(ProductServiceInterface $productService,
                                CategoryServiceInterface $categoryService,
                                OrderServiceInterface $orderService,
                                UserServiceInterface $userService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->orderService = $orderService;
        $this->userService = $userService;
    }


    /**
     * @Route("/product/add", name="productAdd")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productAddAction(Request $request)
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

                $this->addFlash('message', 'Product added successfully.');
                return $this->redirectToRoute("productView", ['id' => $product->getId()]);
            }

            return $this->render('administration/productAdd.html.twig',
                [
                    'form' => $form->createView(),
                    'categories' => $categories,
                    'product' => $product
                ]);
        }

        return $this->render('administration/productAdd.html.twig',
            [
                'categories' => $categories,
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/product/edit/{id}", name="productEdit")
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productEditAction(Request $request, int $id)
    {
        /** @var Product $product */
        $product = $this->productService->getProductById($id);

        $session = $this->get('session');
        $session->set('productToEdit',
            [
                'category' => $product->getCategory(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice()
            ]
        );

        $currentImageName = $product->getImage();
        $categories = $this->categoryService->getAllCategories();
        $form = $this->createForm(ProductType::class, $product, ['validation_groups' => ['Default']]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->getData()->getImage();

            if ($form->isValid()) {
                $uploadDir = $this->getParameter('products_images');
                $this->productService->editProduct($product, $uploadDir, $currentImageName, $imageFile);
                $session->remove('productToEdit');

                $this->addFlash('message', 'Product edited successfully.');
                return $this->redirectToRoute('productView', ['id' => $id]);
            }

            return $this->render('administration/productEdit.html.twig',
                [
                    'form' => $form->createView(),
                    'categories' => $categories,
                    'product' => $product
                ]);
        }

        if ($product) {
            return $this->render('administration/productEdit.html.twig',
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
     * @Route("/orders", name="ordersManage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ordersManageAction()
    {
        /** @var Order[] $orders */
        $orders = $this->orderService->getOrdersByDateDesc();

        return $this->render('administration/orders.html.twig', ['orders' => $orders]);
    }

    /**
     * @Route("/orders/order/{orderId}", name="orderManage")
     * @param int $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderManageAction(int $orderId, Request $request)
    {
        /** @var Order $currentOrder */
        $currentOrder = $this->orderService->viewOrder($orderId);

        if ($currentOrder) {
            $form = $this->createForm(OrderType::class, $currentOrder);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $this->orderService->updateOrderComment($currentOrder);

                $this->addFlash('message', 'Comment updated.');
                return $this->redirectToRoute('orderManage', ['orderId' => $currentOrder->getId()]);
            }

            /** @var OrderItem[] $orderItems */
            $orderItems = $currentOrder->getOrderItems();
            return $this->render('administration/order.html.twig',
                [
                    'orderItems' => $orderItems,
                    'order' => $currentOrder,
                    'form' => $form->createView()
                ]
            );
        }

        $this->addFlash('notice', 'Order does not exists!');
        return $this->redirectToRoute('ordersManage');
    }

    /**
     * @Route("order/updateStatus/{orderId}", name="updateOrderStatus")
     * @param int $orderId
     */
    public function updateOrderStatusAction(int $orderId)
    {
        if ($this->orderService->updateStatus($orderId) === false) {
            $this->addFlash('notice', 'Invalid status given!');
        }

        return $this->redirectToRoute('ordersManage');
    }

    /**
     * @Route("/usersRights", name="usersRights")
     */
    public function usersRightsAction()
    {
        $users = $this->userService->getAllUsers();

        return $this->render('administration/usersRights.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/addedProducts/{userId}", name="addedProducts")
     * @param int $userId
     */
    public function addedProductsAction(int $userId)
    {
        $user = $this->userService->getUserById($userId);

        /** @var Product[] $productsAddedByUser */
        $productsAddedByUser = $user->getUploadedProducts();

        return $this->render('administration/addedProducts.html.twig',
            ['products' => $productsAddedByUser, 'user' => $user]
        );
    }

    /**
     * @Route("/outOfStock", name="outOfStockView")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function outOfStockAction(Request $request)
    {
        $categoryId = $request->query->get('filter');

        /** @var Category[] $categories */
        $categories = $this->categoryService->getAllCategories();

        /** @var Product[] $products */
        $products = $this->productService->outOfStockProducts($categoryId);

        return $this->render('administration/outOfStock.html.twig',
            [
                'products' => $products,
                'categories' => $categories
            ]
        );
    }
}
