<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('misSolicitudes');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'register')]
   public function newUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
   {
       $form = $this->createForm(UserType::class);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $user = $form->getData();
           $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword())); //hasheamos la contraseña.
           $user->setRoles(['ROLE_USER']); //Seteamos el rol que no vamos a setear a mano en el formulario.
           $em->persist($user);
           $em->flush();
           return $this->redirectToRoute('app_login');//Una vez registrado, redireccionamos al login
       }
       return $this->renderForm('/users/createuser.html.twig', ['userForm' => $form]);
   }
}
