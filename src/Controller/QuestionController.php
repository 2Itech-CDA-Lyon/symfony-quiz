<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
     * Require ROLE_USER for only this controller method.
     * 
     * @IsGranted("ROLE_USER")
     */
    public function question_edit(Question $question): Response
    {

        if ($question->getQuiz()->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        } else {
                return $this->render('question/edit.html.twig', [
                    'question' => $question
                ]);
            } 
        
    }
}
