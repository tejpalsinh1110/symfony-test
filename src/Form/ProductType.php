<?php

namespace App\Form;

use App\Entity\Product;
use App\Enum\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Title',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'label' => 'Description',
                'required' => true,
            ])
            ->add('priceExclVat', MoneyType::class, [
                'currency' => 'USD',
                'attr' => ['class' => 'form-control'],
                'label' => 'Price (excl vat)',
                'required' => true,
            ])
            ->add('category', ChoiceType::class, [
                'choices' => ProductCategory::cases(),
                'choice_label' => function (?ProductCategory $category) {
                    return $category ? $category->value : '';
                },
                'placeholder' => 'Select a category',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Product Image',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => ['Default'],
        ]);
    }
}
