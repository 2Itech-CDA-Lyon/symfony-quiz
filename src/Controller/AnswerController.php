<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/answer", name="answer_")
 */
class AnswerController extends AbstractController
{

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, Question $question, AnswerRepository $answerRepo): Response
    {
        $rightAnswer = $answerRepo->find($request->get('rightAnswer'));

        $em = $this->getDoctrine()->getManager();

        $question->setRightAnswer($rightAnswer);

        $answers = $question->getAnswers();

        foreach ($answers as $answer) {
            $answer->setText($request->get('text_' . $answer->getId()));
        }

        $em->persist($question);
        $em->flush();












        $quiz = $question->getQuiz();
        $form = $this->createFormBuilder($quiz)
            ->add('title')
            ->add('description')
            ->add('difficulty')
            ->add('author')
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->add('Supprimer', SubmitType::class, array(
                'label'  => 'Supprimer',
                'attr'   =>  array(
                    'class'   => 'btn btn-danger'
                )
            ))
            ->getForm();

        $this->addFlash('success', 'Réponse modifiée avec succès');
        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"})
     */
    public function delete(Request $request, Answer $answer): Response
    {
        $question = $answer->getQuestion();
        $quiz = $question->getQuiz();
        $form = $this->createFormBuilder($quiz)
            ->add('title')
            ->add('description')
            ->add('difficulty')
            ->add('author')
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->add('Supprimer', SubmitType::class, array(
                'label'  => 'Supprimer',
                'attr'   =>  array(
                    'class'   => 'btn btn-danger'
                )
            ))
            ->getForm();


        $em = $this->getDoctrine()->getManager();
        $em->remove($answer);
        $em->flush();
        $this->addFlash('success', 'Réponse supprimée avec succès');
        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }
}
