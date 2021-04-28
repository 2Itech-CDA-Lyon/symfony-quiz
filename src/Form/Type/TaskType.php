<?php

namespace App\Form\Type;


use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('difficulty', ChoiceType::class, [
                'choices'  => [
                    'Facile' => 1,
                    'Moyen' => 2,
                    'Difficile' => 3,
                ],
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'save',
                'attr' => array(
                    'class' => 'btn btn-primary'
                )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}

?>