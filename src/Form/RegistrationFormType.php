<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                //plainpassword: mdp en clair, au lieu d'etre defi sur l'objet ce champs va etre lu et encodé dans le controlleur. 
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],//remplissage automatique avec le navigateur
                'constraints' => [
                    new NotBlank([ //le mdp ne peut pas etre vide
                        'message' => 'Veuillez taper un mot de passe',
                    ]),
                    new Regex([
                        "pattern"=> "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$/",
                        "message"=> "Le mot de passe doit être composé d'au moins une minuscule, une majuscule, un chiffre, un caractère spécial (+!*$@?), et avoir entre 8 et 15 caractères."
                    ])
                ],
                "required" => false //par defaut il est obligatoire, la on ne le souhaite pas 
            ])
            
            ->add("prenom", TextType::class, [
                "label"=> "Prénom",
                "required"=> false
            ])

            ->add("nom", TextType::class, [
                "required"=>false
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false, //cet input ne fait pas parti des propriete de la classe abonne et il ne va pas essayer de faire correspondre dc pas derreur 
                'constraints' => [ //metre des regles de validation du formulaire , array car on peut avoir plss contraintes= ss forme de classe
                    new IsTrue([ // le champs doit envoyer la valeur true , il doit etre coché sinon : 
                        'message' => 'Vous devez ceepter les CGU (conditions Générales)',
                    ]),
                ],
                "label" => "C.G.U."
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
