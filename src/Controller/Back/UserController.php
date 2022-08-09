<?php
// src\Controller\Back\UserController.php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\AnswerRepository;
use App\Repository\PracticeRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/user", name="back_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['id' => 'DESC']);
        return $this->render('Back/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}/update", name="update", methods={"GET", "POST"})
     */
    public function update(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Votre utilisateur a bien été édité.');
            $userRepository->add($user, true);

            return $this->redirectToRoute('back_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Back/user/update.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository, QuestionRepository $questionRepository, AnswerRepository $answerRepository, PracticeRepository $practiceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->addFlash('success', 'Votre utilisateur a bien été supprimé.');
            $anonymous = $userRepository->findOneBy(['pseudo' => 'Anonymous']);
            if ( $user->getQuestions() ) {
                foreach ($user->getQuestions() as $key => $value) {
                    $value->setUser($anonymous);
                    $questionRepository->add($value, true);
                    $user->removeQuestion($value);
                }
            }
            if ( $user->getPractices() ) {
                foreach ($user->getPractices() as $key => $value) {
                    $value->setUser($anonymous);
                    $practiceRepository->add($value, true);
                    $user->removePractice($value);
                }
            }
            if ( $user->getAnswers() ) {
                foreach ($user->getAnswers() as $key => $value) {
                    $value->setUser($anonymous);
                    $answerRepository->add($value, true);
                    $user->removeAnswer($value);
                }
            }

            $userRepository->remove($user, true);

        return $this->redirectToRoute('back_user_list', [], Response::HTTP_SEE_OTHER);
    }
}
}
