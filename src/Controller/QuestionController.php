<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/question", name="question_")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/{id}", name="single", requirements={"id"="\d+"})
     */
    public function single(Question $question): Response
    {
        return $this->render('question/single.html.twig', [
            'question' => $question,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"}) 
     */
    public function question_edit(Question $question): Response
    {
        return $this->render('question/edit.html.twig', [
            'question' => $question,
        ]);
    }
}
