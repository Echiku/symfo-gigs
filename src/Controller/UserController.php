<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/registers', name: 'user_registers')]
    public function index(): Response
    {
         return $this->render('user/register.html.twig');
    }

    #[Route('/logins', name: 'user_logins')]
    public function login(): Response
    {
        dd("user login");
    }
}
