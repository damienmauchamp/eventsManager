<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

use App\Entity\Label;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, [
                "label" => "Nom"
            ])
            ->add("place", TextType::class, [
                "label" => "Lieu"
            ])
            ->add("dateDebut", DateTimeType::class, [
                "label" => "Début",
                'format' => 'yyyy-MM-dd HH:mm'
            ])
            ->add("dateFin", DateTimeType::class, [
                "label" => "Fin"
            ])/*
            ->add("labels", Select2EntityType::class, [
                'label' => 'Catégories',
                'remote_route' => 'label_autocomplete',
//                'remote_path' => 'label_autocomplete',
                'remote_params' => [
                    "auth" => "ok"
                ],
                'class' => '\\App\Entity\Label',
                'primary_key' => 'id',
                'text_property' => 'name',
                'property' => 'name',
                'multiple' => true,
//                'minimumInputLength' => 2,
                'minimum_input_length' => 3,
                'allow_add' => [
                    'enabled' => true,
                    'new_tag_text' => ' (NEW)',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", ""]'
                ],
//                "language" => 'fr',
                'page_limit' => 10,
//                'allow_clear' => true,
                'delay' => 250
            ])*/
            ->add("labels", Select2EntityType::class, [
                'remote_route' => 'label_autocomplete',
                'class' => '\App\Entity\Label',
                'primary_key' => 'id',
                'text_property' => 'name',
                'property' => 'name',
                'multiple' => true,
                'allow_add' => [
                    'enabled' => true,
                    'new_tag_text' => ' (NEW)',
                    'new_tag_prefix' => '**',
                    'tag_separators' => '[",", ""]'
                ],
                'remote_params' => [
                    "auth" => "ok"
                ],
            ])
            ->add("description", TextareaType::class, [
                "label" => "Description"
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            //'data_class' => Event::class,
        ]);
    }
}
