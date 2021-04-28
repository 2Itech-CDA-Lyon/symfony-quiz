<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quiz", name="quiz_")
 */
class TaskController extends AbstractController
{

    /**
     * @Route("quiz_create", name="quiz_create")
     */
    public function new(Request $request): Response
    {
        // creates a task object and initializes some data for this example
        $quiz = new Quiz();

        $form = $this->createForm(TaskType::class, $quiz);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $quiz = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quiz);
            $entityManager->flush();
            $this->addFlash('success', 'Votre quiz a bien été enregistré !');
            return $this->redirectToRoute('quiz_create');
        }



        return $this->render('quiz/createQuiz.html.twig', [
            'form' => $form->createView(),
        ]);

    }





        
/*     
     * @Route("quiz_create", name="quiz_create")
    
    public function new(Request $request): Response
    {
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

            return $this->render('quiz/createQuiz.html.twig', [
                'form' => $form->createView(),
            ]);

    }
 */
}

?>