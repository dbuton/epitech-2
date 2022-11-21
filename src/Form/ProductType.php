<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'required' => true,
            ])
           ->add('illustration', FileType::class, [
               'label' => 'Image : ',
               'mapped' => false,
               'required' => false,
               'constraints' => [
                   new File([
                       'maxSize' => '1024k',
                       'mimeTypes' => [
                           'image/jpeg',
                           'image/png',
                       ],
                       'mimeTypesMessage' => 'La photo doit être en jpeg ou png.',
                   ])
               ],
           ])
            ->add('subtitle', TextType::class, [
               'label' => 'Titre : ',
           ])
            ->add('description',TextareaType::class, [
                'label' => 'Description : ',
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix : ',
                'required' => true,
            ])
            ->add('isBest', CheckboxType::class, [
                'label' => 'Meilleurs ventes',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie : ',
                'required' => false,
                'class' => Category::class,
                'multiple' => false,
                'expanded' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
