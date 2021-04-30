<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerFormType;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/answer", name="answer_")
 */
class AnswerController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="new", requirements={"id"="\d+"})
     */
    public function new(Question $question, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur authentifié a le droit de modifier la question demandée selon la politique de permissions définie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $question);
        $answer = new Answer();
        // Crée un formulaire associé à la réponse
        $form = $this->createForm(AnswerFormType::class, $answer);
        // Demande au formulaire de récupérer sur le contenu de la requête
        $form->handleRequest($request);
        // Si le formulaire vient d'ëtre envoyé (requête POST) et qu'il ne contient aucune erreur
        if ($form->isSubmitted() && $form->isValid()) {
            // Associe la réponse à la question demandée
            $rightAnswer = $question->getRightAnswer();
            $question->addAnswer($answer);
            // Créer la réponse en base de données
            $entityManager->persist($answer);
            $question->setRightAnswer($rightAnswer);
            $entityManager->persist($question);
            $entityManager->flush();
            $this->addFlash('success', 'Réponse ajoutée avec succès!');
            // Redirige sur la page "modifier une question"
            return $this->redirectToRoute('question_edit', ['id' => $answer->getQuestion()->getId()]);
        }
        // Affiche la page "créer une réponse"
        return $this->render('answer/edit.html.twig', [
            'verb' => 'Ajouter',
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     */
    public function edit(Answer $answer, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur authentifié a le droit de modifier la réponse demandée selon la politique de permissions définie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $answer);
        // Crée un formulaire associé à la réponse
        $form = $this->createForm(AnswerFormType::class, $answer);
        // Demande au formulaire de récupérer sur le contenu de la requête
        $form->handleRequest($request);
        // Si le formulaire vient d'ëtre envoyé (requête POST) et qu'il ne contient aucune erreur
        if ($form->isSubmitted() && $form->isValid()) {
            // Modifie la réponse en base de données
            $entityManager->persist($answer);
            $entityManager->flush();
            $this->addFlash('success', 'Réponse modifiée avec succès!');
            // Redirige sur la page "modifier une question"
            return $this->redirectToRoute('question_edit', ['id' => $answer->getQuestion()->getId()]);
        }
        // Affiche la page "modifier une réponse"
        return $this->render('answer/edit.html.twig', [
            'verb' => 'Modifier',
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"})
     */
    public function delete(Answer $answer, EntityManagerInterface $entityManager, AnswerRepository $repository): Response
    {
        // Vérifie que l'utilisateur authentifié a le droit de modifier la réponse demandée selon la politique de permissions définie dans les voters
        $this->denyAccessUnlessGranted('EDIT', $answer);
        // Si la réponse que l'on cherche à supprimer est la bonne réponse à la question, redéfinit la bonne réponse d'abord
        $question = $answer->getQuestion();
        if ($answer === $question->getRightAnswer()) {
            // Récupère la première réponse autre que celle qu'on cherche à supprimer
            $otherAnswer = $repository->findAnotherOneInSameQuestion($answer);
            // Si aucune autre réponse n'a été trouvée, c'est donc qu'on cherche à supprimer la dernière réponse existante
            if (is_null($otherAnswer)) {
                $this->addFlash('danger', 'Vous ne pouvez pas supprimer la dernière réponse à cette question.');
                return $this->redirectToRoute('question_edit', ['id' => $question->getId()]);
            }
            // Sinon, associe la réponse trouvée en tant que bonne réponse à la question
            $question->setRightAnswer($otherAnswer);
            $entityManager->persist($question);
        }
        // Supprime la réponse en base de données
        $entityManager->remove($answer);
        $entityManager->flush();
        $this->addFlash('success', 'Réponse supprimée avec succès!');
        // Redirige vers la page "modifier une question"
        return $this->redirectToRoute('question_edit', ['id' => $question->getId()]);
    }
}
