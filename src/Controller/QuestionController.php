<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerType;
use App\Form\QuestionFormType;
use Symfony\Component\Form\Form;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @Route("/new/{id}", name="new", requirements={"id":"\d+"})
     */
    public function new(Quiz $quiz, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur authentifié a le droit de modifier le quiz demandé selon la politique de permissions définie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $quiz);
        // Crée une nouvelle question
        $question = new Question();
        // Crée un formulaire associé à la question
        $form = $this->createForm(QuestionFormType::class, $question);
        // Demande au formulaire de récupérer sur le contenu de la requête
        $form->handleRequest($request);
        // Si le formulaire vient d'ëtre envoyé (requête POST) et qu'il ne contient aucune erreur
        if ($form->isSubmitted() && $form->isValid()) {
            // Créer une nouvelle réponse pour faire office de première réponse à la question
            $firstAnswer = new Answer();
            $firstAnswer
                ->setText('La réponse A')
            ;
            // Complète l'objet question avec les valeurs non présentes dans le formulaire
            $question
                // Associe la question au quiz demandé
                ->setQuiz($quiz)
                // Donne comme valeur d'ordre le nombre de questions actuellement existantes dans le quiz, plus 1
                ->setOrder(count($quiz->getQuestions()) + 1)
                // Associe la première réponse à la question
                ->addAnswer($firstAnswer)
                // Associe la première réponse comme bonne réponse à la question
                ->setRightAnswer($firstAnswer)
            ;
            // Crée la question en base de données
            $entityManager->persist($question);
            $entityManager->flush();
            $this->addFlash('success', 'Question créée avec succès!');
            // Redirige sur la page "modifier une question"
            return $this->redirectToRoute('question_edit', ['id' => $question->getId()]);
        }
        // Affiche la page "modifier une question"
        return $this->render('question/edit.html.twig', [
            'verb' => 'Ajouter',
            'question' => $question,
            'quiz_id' => $quiz->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur authentifié a le droit de modifier la question demandée selon la politique de permissions définie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $question);
        // Crée un formulaire associé à la question
        $form = $this->createForm(QuestionFormType::class, $question);
        // Demande au formulaire de récupérer sur le contenu de la requête
        $form->handleRequest($request);
        // Si le formulaire vient d'ëtre envoyé (requête POST) et qu'il ne contient aucune erreur
        if ($form->isSubmitted() && $form->isValid()) {
            // Modifie la question en base de données
            $entityManager->persist($question);
            $entityManager->flush();
            $this->addFlash('success', 'Question modifiée avec succès!');
        }
        // Affiche la page "modifier une question"
        return $this->render('question/edit.html.twig', [
            'verb' => 'Modifier',
            'question' => $question,
            'quiz_id' => $question->getQuiz()->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", requirements={"id":"\d+"}, methods={"POST"})
     */
    public function delete(Question $question, EntityManagerInterface $entityManager, QuestionRepository $questionRepository)
    {
        // Vérifie que l'utilisateur authentifié a le droit de modifier la question demandée selon la politique de permissions définie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $question);
        // Supprime la question en base de données
        $entityManager->remove($question);
        $entityManager->flush();

        // Récupère toutes les questions appartenant au même quiz, et avec un ordre strictement supérieur à celui de la question supprimée
        $questionsToReorder = $questionRepository->findInSameQuizWithGreaterOrder($question);
        // Pour chacune de ces questions
        foreach($questionsToReorder as $questionToReorder){
            // Décrémente son ordre de 1
            $questionToReorder->setOrder($questionToReorder->getOrder() - 1);
            // Sauvegarde le changement en base de donnéess
            $entityManager->persist($questionToReorder);
        }
        $entityManager->flush();

        $this->addFlash('success', 'Question supprimée avec succès!');
        // Redirige sur la page "modifier un quiz"
        return $this->redirectToRoute('quiz_edit', ['id' => $question->getQuiz()->getId()]);
    }

    /**
     * @Route("/{id}/right_answer/{answerId}", name="right_answer", methods={"POST"}, requirements={"id":"\d+", "answerId":"\d+"})
     */
    public function rightAnswer(Question $question, int $answerId, AnswerRepository $answerRepository, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur authentifié a bien le droit de modifier la question demandée
        $this->denyAccessUnlessGranted('EDIT', $question);
        // Vérifie que la réponse avec l'ID spécifié existe en base de données
        $answer = $answerRepository->find($answerId);
        if (is_null($answer)) {
            return new JsonResponse([ 'message' => 'Answer #' . $answerId . ' does not exist.'], Response::HTTP_NOT_FOUND);
        }
        // Vérifie que la réponse appartient bien à la question
        if ($answer->getQuestion() !== $question) {
            return new JsonResponse([ 'message' => 'Answer #' . $answerId . ' does not belong to question #' . $question->getId() . '.' ], Response::HTTP_BAD_REQUEST);
        }
        // Modifie la bonne réponse associée à la question
        $question->setRightAnswer($answer);
        $entityManager->persist($question);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{id}/answer", name="answer", methods={"POST"}, requirements={"id":"\d+"})
     */
    public function answer(Question $question, QuestionRepository $questionRepo, Request $request): Response
    {
        // Si le formulaire ne contient pas de numéro de réponse, renvoie sur la même question avec un message d'erreur
        if (is_null($request->get('answer')) || empty($request->get('answer'))) {
            $this->addFlash('danger', 'Aucune réponse n\'a été fournie.');
            return $this->redirectToRoute('question_single', ['id' => $question->getId()]);
        }

        // Si l'utilisateur a donné la bonne réponse à la question demandée
        if ($question->getRightAnswer()->getId() == $request->get('answer')){
            $this->addFlash('success', '👍 Bravo, c\'était la bonne réponse!');
        // Sinon
        } else {
            $this->addFlash('danger', '👎 Hé non! La bonne réponse était: ' . $question->getRightAnswer()->getText());
        }

        // Cherche la question...
        $nextQuestion = $questionRepo->findOneBy([
            // ...qui apparttient au même quiz que la question à laquelle on vient de répondre...
            'quiz' => $question->getQuiz(),
            // ...et qui la suit directement dans l'ordre
            'order' => $question->getOrder() + 1
        ]);

        // S'il n'y a pas de question suivante, c'est donc qu'on a atteint la fin du quiz
        if ($nextQuestion === null){
            $this->addFlash('info', 'Quiz terminé !');
            return $this->redirectToRoute('quiz_list');
        }

        // Sinon, redirige sur la page présentant la question suivante
        return $this->redirectToRoute('question_single', ['id' => $nextQuestion->getId()]);
    }
}
