<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   $abonne = $options["data"];//La variable entité utilisée pour créer le formulaire se trouve dans $options["data"]
        $builder
            ->add('pseudo')
            ->add('roles',ChoiceType::class,[
                "choices" => [
                    "Lecteur" => "ROLE_LECTEUR",
                    "Bibliotheque" => "ROLE_BIBLIO",
                    "Directeur"=> "ROLE_ADMIN",
                    "Abonné"=>"ROLE_USER",
                    "développeur"=>"ROLE_DEV"
                ],
                "multiple"=>true,
                "expanded"=>true,
                "label"=>"Autorisations"
            ])
            ->add('password', TextType::class, [
                "required"=> $abonne->getId() ? false : true,// si l'id n'est pas vide alors password n'est pas requis
                "mapped"=>false //mapped false permet de ne pas lier l'input password à la propriete password de l'objet abonné . pr ne pas que l'ancien  mdp qui s'affiche
            ] )
            ->add('nom')
            ->add('prenom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}