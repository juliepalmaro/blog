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
                'required' => true,
            ])
            ->add('subTitle', TextType::class, [
                'label' => 'Sous-titre',
                'required' => true,
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'required' => true,
            ])
            ->add('readingTime', NumberType::class, [
                'label' => 'Temps de lecture',
                'required' => true,
            ])
            ->add('picture', TextType::class, [
                'label' => 'URL Image',
                'required' => false,
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'Publier l\'article',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
