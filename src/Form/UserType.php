<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username", TextType::class, [
                "label" => "Nom d'utilisateur"
            ])
            ->add("password", PasswordType::class, [
                "label" => "Mot de passe"
            ])
            ->add("firstname", TextType::class, [
                "label" => "Prénom"
            ])
            ->add("lastname", TextType::class, [
                "label" => "Nom"
            ])
            ->add("email", EmailType::class, [
                "label" => "Adresse mail"
            ])
            ->add("valider", SubmitType::class, [
                "label" => "S'incrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            //'data_class' => User::class,
        ]);
    }
}
