<?php

namespace App\Form;

use App\Entity\CandidateResume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomcomplet',TextType::class)
            ->add('telephone',TelType::class)
            ->add('email',EmailType::class)
            ->add('imageFile',VichImageType::class,[
                'required'=>false,
                'allow_delete'=>true,
                'download_link'=>true
            ])
            ->add('presentation',TextareaType::class)
            ->add('cvFile',VichFileType::class,[
                'required'=>false
            ])
            ->add ('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CandidateResume::class,
        ]);
    }
}
