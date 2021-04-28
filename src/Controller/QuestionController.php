<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\AnswerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

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
    public function edit(Request $request, Question $question): Response
    {

        $form = $this->createFormBuilder($question)
            ->add('text')
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->add('Supprimer', SubmitType::class, array(
                'label'  => 'Supprimer',
                'attr'   =>  array(
                    'class'   => 'btn btn-danger'
                )
            ))
            ->getForm();

        if ($form instanceof Form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if ($form->getClickedButton() === $form->get('Supprimer')) {
                    $this->addFlash('success', 'Question modifiée avec succès');
                    $em->remove($question);
                } else if ($form->getClickedButton() === $form->get('save')) {
                    $this->addFlash('success', 'Question supprimée avec succès');
                    $em->persist($question);
                }
                $em->flush();
                return $this->redirectToRoute('quiz_edit', ['id' => (int)$question->getQuiz()->getId()]);
            }
        }

        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }
}
