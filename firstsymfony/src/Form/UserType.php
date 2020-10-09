<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['isEditingTeam'] == "false") {
            $builder
                ->add('email')
                ->add('firstName')
                ->add('lastName')
                ->add('team', EntityType::class, [
                    'class' => Team::class,
                    'choice_label' => 'name'
                ])
            ;
        }
        else {
            $builder
                ->add('team', EntityType::class, [
                    'class' => Team::class,
                    'choice_label' => 'name'
                ])
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isEditingTeam' => 'false'
        ]);
    }
}
