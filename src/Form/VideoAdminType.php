<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
USE App\Entity\User;
USE Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VideoAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',

            ])
            ->add('youtubeUrl')
            ->add('description')
            ->add('category', EntityType::class,[
                'multiple'=>true,
                'expanded' => true,
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('isActive', CheckboxType::class, [
                'label'    => 'Publié ?',
                'required' => true,
            ])
            ->add('user', EntityType::class, [
              'class' => User::class,
            'choice_label' => 'email',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
