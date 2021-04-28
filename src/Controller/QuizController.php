<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * Require ROLE_USER for only this controller method.
     * 
     * @IsGranted("ROLE_USER")
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
     * Require ROLE_USER for only this controller method.
     * 
     * @IsGranted("ROLE_USER")
     */
    public function edit(Quiz $quiz): Response
    {

        if ($quiz->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        } else {
                return $this->render('quiz/edit.html.twig', [
                    'quiz' => $quiz
                ]);
            } 
    }
}
