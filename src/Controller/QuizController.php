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
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, QuizRepository $repository, EntityManagerInterface $entityManager): Response
    {
        // Cr??e un nouveau quiz
        $quiz = new Quiz();
        // Cr??e un formulaire associ?? au quiz
        $form = $this->createForm(QuizType::class, $quiz);
        // Demande au formulaire de r??cup??rer sur le contenu de la requ??te
        $form->handleRequest($request);
        // Si le formulaire vient d'??tre envoy?? (requ??te POST) et qu'il ne contient aucune erreur
        if ($form->isSubmitted() && $form->isValid()) {
            // D??finit l'utilisateur authentifi?? comme auteur du quiz
            $quiz->setAuthor($this->getUser());
            // Modifie le quiz de la base de donn??es
            $entityManager->persist($quiz);
            $entityManager->flush();
            $this->addFlash('success', 'Quiz ajout?? avec succ??s');
            // Redirige sur la page "modification de quiz"
            return $this->redirectToRoute('quiz_edit', ['id' => $quiz->getId()]);
        }
        // Affiche la page "cr??er un quiz"
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
    public function edit(Quiz $quiz, Request $request, EntityManagerInterface $entityManager, QuestionRepository $questionRepository): Response
    {
        // V??rifie que l'utilisateur authentifi?? a le droit de modifier le quiz demand?? selon la politique de permissions d??finie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $quiz);
        // Cr??e un formulaire associ?? au quiz
        $form = $this->createForm(QuizType::class, $quiz);
        // Demande au formulaire de r??cup??rer sur le contenu de la requ??te
        $form->handleRequest($request);
        // Si le formulaire vient d'??tre envoy?? (requ??te POST) et qu'il ne contient aucune erreur
        if ($form->isSubmitted() && $form->isValid()) {
            // Modifie le quiz de la base de donn??es
            $entityManager->persist($quiz);
            $entityManager->flush();
            $this->addFlash('success', 'Quiz modifi?? avec succ??s');
        }

        $questions = $questionRepository->findBy(
            ['quiz' => $quiz],
            ['order' => 'ASC']
        );

        // Affiche la page "modifier un quiz"
        return $this->render('quiz/edit.html.twig', [
            'verb' => 'Modifier',
            'quiz' => $quiz,
            'form' => $form->createView(),
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function delete(Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        // V??rifie que l'utilisateur authentifi?? a le droit de modifier le quiz demand?? selon la politique de permissions d??finie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $quiz);
        // Supprime le quiz de la base de donn??es
        $entityManager->remove($quiz);
        $entityManager->flush();
        // Redirige sur la page "cr??ation de quiz"
        return $this->redirectToRoute('quiz_create');
    }
}
