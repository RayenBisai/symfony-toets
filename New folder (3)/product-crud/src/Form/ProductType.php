<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Productnaam',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Voer productnaam in'],
                'constraints' => [new NotBlank()],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschrijving',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Voer beschrijving in'],
                'constraints' => [new NotBlank()],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prijs (€)',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00'],
                'constraints' => [new NotBlank()],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Voorraad',
                'attr' => ['class' => 'form-control', 'placeholder' => '0'],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Selecteer een categorie',
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Opslaan',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
