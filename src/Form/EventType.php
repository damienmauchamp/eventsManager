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
                "label" => "Début"
            ])
            ->add("dateFin", DateTimeType::class, [
                "label" => "Fin"
            ])
            ->add("labels", Select2EntityType::class, [
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
                'allow_add' => [
                    'enabled' => true,
                    'new_tag_text' => ' (NEW)',
                    'new_tag_prefix' => '__',
                    'tag_separators' => '[",", ""]'
                ],
                "language" => 'fr',
                'minimum_input_length' => 2,
                'page_limit' => 10,
                'allow_clear' => true,
                'delay' => 250
            ])
            ->add("description", TextareaType::class, [
                "label" => "Description"
            ])
            ->add("valider", SubmitType::class, [
                "label" => "Créer l'évènement"
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