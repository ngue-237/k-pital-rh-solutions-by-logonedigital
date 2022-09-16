<?php

namespace App\Form;

use App\Entity\CandidateResume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomcomplet',TextType::class)
            ->add('telephone',TelType::class)
            ->add('email',EmailType::class)
            ->add('photo',FileType::class,[
                'label'=>'Photo de Profil',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File([
                        'mimeTypes'=>[
                            'image/jpeg',
                            'image/png',
                            'image/jpg'
                        ],
                        'mimeTypesMessage'=>'Entrez une image valide (png,jpg,jpeg)!'
                    ])
                ]
            ])
            ->add('cv', FileType::class, [
                'label' => 'CV (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('presentation',TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CandidateResume::class,
        ]);
    }
}
