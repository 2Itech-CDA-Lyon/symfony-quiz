<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question/{id}", name="question_single", requirements={"id"="\d+"})
     */
    public function single(int $id, QuestionRepository $questionRepository): Response
    {
        $question = $questionRepository->find($id);

        if (is_null($question)) {
            throw $this->createNotFoundException('Question #' . $id . ' does not exist.');
        }

        return $this->render('question/single.html.twig', [
            'question' => $question,
        ]);
    }
}
