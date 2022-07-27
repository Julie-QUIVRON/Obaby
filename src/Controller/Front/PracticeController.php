<?php
// src/Controller/Front/PracticeController

namespace App\Controller\Front;

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Practice;
use App\Form\AnswerType;
use App\Form\PracticeType;
use App\Repository\CategoryRepository;
use App\Repository\PracticeRepository;
use App\Services\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PracticeController extends AbstractController
{


//* route for practices list

    /**
     * @Route("/category/{slug}/practice/list", name="practice_list", methods={"GET"})
     */
    public function list(Category $category, CategoryRepository $categoryRepository): Response
    {
        $practices = $category->getPractices();
        $categories = $categoryRepository->findAll();

        return $this->render('Front/practice/list.html.twig', [
            'practices' => $practices,
            'categories' => $categories,
        ]);
    }

//* Route used to submit a new practice

    /**
     * @Route("/practice/submit", name="practice_submit", methods={"GET", "POST"})
     */
    public function submit(Request $request, PracticeRepository $practiceRepository, SlugService $slugService): Response
    {
        $practice = new Practice();
        $form = $this->createForm(PracticeType::class, $practice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Votre bonne pratique a bien été enregistrée. Elle est en attente de modération.');
            $practice->setSlug($slugService->slug($practice->getTitle()));
            $practiceRepository->add($practice, true);

            return $this->redirectToRoute('category_show_practice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Front/practice/new.html.twig', [
            'practice' => $practice,
            'form' => $form,
        ]);
    }
}
