<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
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
    public function create(Request $request, QuizRepository $repository): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser->hasRole('ROLE_ADMIN')) {
            $quizzes = $repository->findAll();
        } else {
            $quizzes = $currentUser->getQuizzes();
        }

        return $this->render('quiz/create.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request, QuizRepository $repository): Response
    {
        $quiz = new Quiz();

        $form = $this->createForm(QuizType::class, $quiz);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('success', 'Quiz ajouté avec succès');
            return $this->redirectToRoute('quiz_create');
        }

        return $this->render('quiz/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     * Require ROLE_USER for only this controller method.
     * 
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Quiz $quiz): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $quiz);

        $form = $this->createFormBuilder($quiz)
            ->add('title')
            ->add('description')
            ->add('difficulty')
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
                    $this->addFlash('success', 'Quiz modifié avec succès');
                    $em->remove($quiz);
                } else if ($form->getClickedButton() === $form->get('save')) {
                    $this->addFlash('success', 'Quiz supprimé avec succès');
                    $em->persist($quiz);
                }
                $em->flush();
                return $this->redirectToRoute('quiz_create');
            }
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }
}
