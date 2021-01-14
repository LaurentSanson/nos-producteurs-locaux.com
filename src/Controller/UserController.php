<?php

namespace App\Controller;

use App\Handler\UserInfoHandler;
use App\Handler\UserPasswordHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserInfoHandler $handler
     * @return Response
     * @Route("/edit-infos", name="user_edit_infos")
     */
    public function editInfo(Request $request, UserInfoHandler $handler): Response
    {
        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute("user_edit_infos");
        }

        return $this->render("ui/user/edit_infos.html.twig", [
            "form" => $handler->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordHandler $handler
     * @return Response
     * @Route("/edit-password", name="user_edit_password")
     */
    public function editPassword(Request $request, UserPasswordHandler $handler): Response
    {
        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute("user_edit_password");
        }

        return $this->render("ui/user/edit_password.html.twig", [
            "form" => $handler->createView()
        ]);
    }
}
