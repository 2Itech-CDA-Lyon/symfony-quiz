<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question/{id}", name="question_single", requirements={"id"="\d+"})
     */
    public function single(Question $question, QuestionRepository $questionRepository): Response
    {
        return $this->render('question/single.html.twig', [
            'question' => $question,
        ]);
    }
}
