<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use Symfony\Component\Form\Form;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Require ROLE_USER for only this controller method.
     * 
     * @IsGranted("ROLE_USER")
     */
    public function create(QuizRepository $repository): Response
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
    public function new(Request $request, QuizRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();

        $form = $this->createForm(QuizType::class, $quiz);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $data->setAuthor($this->getUser());

            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('success', 'Quiz ajouté avec succès');
            return $this->redirectToRoute('quiz_edit', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/edit.html.twig', [
            'verb' => 'Ajouter',
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     * Require ROLE_USER for only this controller method.
     * 
     * @IsGranted("ROLE_USER")
     */
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $quiz);

        $form = $this->createForm(QuizType::class, $quiz);

        if ($form instanceof Form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($quiz);
                $entityManager->flush();
                $this->addFlash('success', 'Quiz modifié avec succès');
            }
        }

        return $this->render('quiz/edit.html.twig', [
            'verb' => 'Modifier',
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function delete(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $quiz);

        $entityManager->remove($quiz);
        $entityManager->flush();
        return $this->redirectToRoute('quiz_create');
    }
}
