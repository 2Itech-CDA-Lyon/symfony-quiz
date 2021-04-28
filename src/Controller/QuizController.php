<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quiz", name="quiz_")
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function list(QuizRepository $repository): Response
    {
        $quizzes = $repository->findAll();

        return $this->render('quiz/list.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @Route("/{id}", name="single", requirements={"id"="\d+"})
     */
    public function single(Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $firstQuestion = $questionRepository->findOneBy(['order' => 1, 'quiz' => $quiz]);

        return $this->render('quiz/single.html.twig', [
            'quiz' => $quiz,
            'firstQuestion' => $firstQuestion,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(QuizRepository $repository): Response
    {
        $quizzes = $repository->findAll();

        return $this->render('quiz/create.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     */
    public function edit(Quiz $quiz): Response
    {
        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz
        ]);
    }


    /**
     * @Route("quiz_delete", name="quiz_delete")
     */
    public function delete(Quiz $quiz): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($quiz);
        $entityManager->remove($quiz);
        $entityManager->flush();
        $this->addFlash('success', 'Votre quiz a bien été supprimé !');
        return $this->redirectToRoute('quiz_create');
        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz
        ]);
    }
}
