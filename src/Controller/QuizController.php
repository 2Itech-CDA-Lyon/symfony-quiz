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
}
