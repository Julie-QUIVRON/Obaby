<?php
//src/Controller/Front/UserController


namespace App\Controller\Front;

use App\Entity\User;
use App\Form\NewUserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
//* Route to add a new user

    /**
     * @Route ("/new", name="new", methods={"GET","POST"})
     * @param int $id
    */
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(NewUserType::class, $user);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword= $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $user->setStatus(1);
            $user->setRoles(['ROLE_USER']);
            $userRepository->add($user, true);

            return $this->redirectToRoute('user_new', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('Front/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

//* Route to show personnal informations
    /**
     * @Route ("/{id}", name="show", methods={"GET"})
    */
    public function show(User $user): Response
    {
        return $this->render('Front/user/personnalInformation.html.twig', [
            'user' => $user,
        ]);
    }
    
//* Route to update personnal informations
    /**
     * @Route ("/{id}/update", name="update", methods={"POST", "GET"})
     * @param int $id
    */
    public function update(User $user, Request $request, UserRepository $userRepository): Response
    {
        //create form with User
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);
            return $this->redirectToRoute('Front/user/personnalInformation.html.twig');
        }  
        
        return $this->renderForm('Front/user/update.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}