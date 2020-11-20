<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleNewType extends AbstractType
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
            ])
            ->add('subTitle', TextType::class, [
                'label' => 'Sous-titre',
            ])
            ->add('readingTime', NumberType::class, [
                'label' => 'Temps de lecture',
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'Publier l\'article',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
