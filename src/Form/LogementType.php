<?php

namespace App\Form;

use App\Entity\Logement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TransformationFailureExtension;
use Symfony\Component\Form\Extension\HttpFoundation\Type\FormTypeHttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Extension\Validator\Type\UploadValidatorExtension;
use Symfony\Component\Form\Extension\Csrf\Type\FormTypeCsrfExtension;
use Symfony\Component\Form\Extension\DataCollector\Type\DataCollectorTypeExtension;
use EasyCorp\Bundle\EasyAdminBundle\Form\Extension\EasyAdminExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class LogementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Champ titre
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un titre'
                    ]),

                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'max' => 200,
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères'
                    ]),
                ]
            ])
            // Champ type
            ->add('type', ChoiceType::class, [
                'label' => 'Type de transaction',
                'choices'  => [
                    'Vente' => true,
                    'Location' => false,
                ],
            ])
            // Champ prix
            ->add('price', MoneyType::class, [
                'label' => 'Prix / Loyer mensuel (en euros)',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un prix/loyer'
                    ]),
                    new Length([
                        'max' => 11,
                        'maxMessage' => 'Merci d\'être raisonnable sur le prix'
                    ]),
                ]
            ])
            // Champ description
            ->add('description', CKEditorType::class, [
                'label' => 'Description (soyez le plus exhaustif possible)',
                'purify_html' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner une description'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'La descritpion doit contenir au moins {{ limit }} caractères',
                        'max' => 20000,
                        'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères'
                    ]),
                ]
            ])
            // Champ photo
            ->add('main_photo', FileType::class, [
                'label' => 'Sélectionnez une nouvelle photo',
                'attr' => [
                    'accept' => 'image/jpeg, image/png'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'L\'image doit être de type jpeg ou png',
                        'maxSizeMessage' => 'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximum autorisée est {{ limit }}{{ suffix }}',
                    ]),
                    new NotBlank([
                        'message' => 'Vous devez sélectionner un fichier',
                    ])
                ]
            ])

            // Champ rooms
            ->add('rooms', IntegerType::class, [
                'label' => 'Nombre de chambres',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un nombre de chambres'
                    ]),
                    new Length([
                        'min' => 0,
                        'max' => 600,
                        'maxMessage' => 'Merci d\'être raisonnable sur le nombre de chambres'
                    ]),
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-outline-primary col-12'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Logement::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
