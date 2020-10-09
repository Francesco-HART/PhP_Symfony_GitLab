<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gitlabId',TextareaType::class,[
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description',TextareaType::class,[
                'attr' => ['class' => 'form-control'],
            ])
            ->add('name',TextareaType::class,[
                'attr' => ['class' => 'form-control'],
            ])
            ->add('created_at',TextareaType::class,[
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
