<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [

                    // new NotBlank([
                    //     'message' => 'veuillez saisir un titre !'
                    // ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => "veuillez saisir un titre d'au moins 10 caractéres"
                    ])
                ],
            ])
            ->add('description')
            ->add('couleur')
            ->add('taille')
            ->add('photoForm', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('prix')
            ->add('stock')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Choisissez une categorie'
            ])
            ->add('envoyer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
