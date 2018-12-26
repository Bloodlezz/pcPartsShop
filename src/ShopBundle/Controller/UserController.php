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
     * @Route("/profile", name="profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction()
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render('user/profile.html.twig', ['user' => $currentUser]);
    }

    /**
     * @Route("/profile/edit", name="profileEdit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $currentHashedPass = $this->getUser()->getPassword();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => 'Default']);
        $form->handleRequest($request);

//        if ($request->isMethod('POST')) {
//            $form->submit($request->request->get($form->getName()), false);
//        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->userService->edit($user, $currentHashedPass);

                return $this->redirectToRoute("homepage");
            }

            return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
