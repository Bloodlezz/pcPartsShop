<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;
use ShopBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package ShopBundle\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @var UserServiceInterface $userService
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['Default', 'Register']]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->userService->register($user);

                return $this->redirectToRoute("homepage");
            }

            return $this->render('user/register.html.twig', ['form' => $form->createView(), 'user' => $user]);
        }

        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/profile/edit", name="profileEdit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $session = $this->get('session');
        $session->set('currentUser',
            [
                'firstName' => $this->getUser()->getFirstName(),
                'lastName' => $this->getUser()->getLastName(),
                'email' => $this->getUser()->getEmail(),
                'phone' => $this->getUser()->getPhone()
            ]
        );

        /** @var User $user */
        $user = $this->getUser();
        $currentHashedPass = $user->getPassword();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => 'Default']);
        $form->handleRequest($request);

//        if ($request->isMethod('POST')) {
//            $form->submit($request->request->get($form->getName()), false);
//        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->userService->edit($user, $currentHashedPass);
                $session->remove('currentUser');

                $this->addFlash('message', 'Profile changed successfully.');
                return $this->redirectToRoute("profileEdit", ['ok' => 1]);
            }

            return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    /**
     * @Route("/editRoles/{userId}/{roleId}", name="editRoles")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param int $userId
     * @param int $roleId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRolesAction(int $userId, int $roleId)
    {
        $this->userService->editRoles($userId, $roleId);

        $this->addFlash('message', 'Change successful.');
        return $this->redirectToRoute('usersRights');
    }

    /**
     * @Route("/wishList/edit/{productId}", name="wishListEdit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $productId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editWishListAction(int $productId)
    {
        if ($this->userService->editWishList($productId)) {
            $this->addFlash('message', 'Product added to your wish list.');
        } else {
            $this->addFlash('message', 'Product removed from your wish list.');
        }

        return $this->redirectToRoute('productView', ['id' => $productId]);
    }
}
