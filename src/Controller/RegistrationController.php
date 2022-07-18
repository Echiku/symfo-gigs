<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, UserRepository $userRepo): Response
    {
       

           $name=$request->request->get('name');
           $email=$request->request->get('email');
           $password=$request->request->get('password');
           $password2=$request->request->get('password2');

           if($name && $email && $password && $password2){

           //check email format

              $checkEmail=$userRepo->findOneByEmail($email);

              $check=filter_var($email, FILTER_VALIDATE_EMAIL);

              if($checkEmail){

                $message="There is already an account with this email !";
               
                return $this->render('registration/register.html.twig',[
                   
                    'email_error'=>$message,
                
                 ]);

              }

                if($check == false){

                    $message="Please enter a valid email!";

                    return $this->render('registration/register.html.twig',[
                    
                        'email_error'=>$message,
                    
                    ]);

                }


            // check passwords

                if($password != $password2){
                    
                    $message = 'Passwords are not the same';
                    return $this->render('registration/register.html.twig',[
                        
                        'email_password'=>$message,
                    
                    ]);

                }
            
           

           //Create new user

           $user = new User();

           $user->setName($name)
                ->setEmail($email)
                ->setPassword($userPasswordHasher->hashPassword($user,$password));
           
           $entityManager->persist($user);
           $entityManager->flush();

           
           return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );

    }
           
        
        return $this->render('registration/register.html.twig');
        
    }
}
