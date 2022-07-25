<?php
// src/Controller/Front/QuestionController

namespace App\Controller\Front;

use App\Entity\Category;
use App\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use App\Entity\Answer;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends AbstractController
{

//* Route for listing question's category

    /**
     * @Route("/category/{id}/question/list", name="question_list", methods={"GET"})
     */
    public function list(Category $category): Response
    {
        $questions = $category->getQuestions();

        //! answers have to be displayed in twig with a loop
        //$answers = $questions->getAnswers();

        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);

        return $this->render('Front/question/list.html.twig', [
            'questions' => $questions,
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

//* Route used to submit a new answer

    /**
     * @Route("/question/{id}/answer", name="question_answer", methods={"POST"})
     */
    public function answer(Question $question, Request $request, AnswerRepository $answerRepository): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setQuestion($question);

            $answerRepository->add($answer, true);

            return $this->redirectToRoute('question_list', [
                'id' => $question->getCategory()->getId()
            ]);
        }

        return $this->renderForm('Front/question/answer.html.twig', [
            'question' => $question,
            'form' => $form->createView()
        ]);
    }

//* Route to ask a new question

    /**
     * @Route("/question/ask", name="question_ask", methods={"GET", "POST"})
     */
    public function ask(Request $request, QuestionRepository $questionRepository): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $questionRepository->add($question, true);

            return $this->redirectToRoute('category_show_question', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Front/question/ask.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

}
