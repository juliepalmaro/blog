<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationFormType extends AbstractType
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $encoder = $this->encoder;
        $builder
            ->add('firstName', TextareaType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextareaType::class, [
                'label' => 'Nom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('phone', TextareaType::class, [
                'label' => 'Numéro de téléphone'
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe sont différents',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de Passe'],
                'second_options' => ['label' => 'Confirmation de mot de passe'],
            ])
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (RoleRepository $repo) {
                    return $repo->createQueryBuilder('r');
                }
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($encoder) {
                /** @var USer */
                $user = $event->getData();
                $form = $event->getForm();
                if ($user) {
                    $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    /** @var Role */
                    foreach ($user->getUserRoles()->toArray() as $role) {
                        $role->addUser($user);
                    }
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
