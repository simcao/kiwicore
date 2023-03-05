<?php
/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */

namespace App\Form;

use App\Entity\ProductStockTransaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductStockTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('transactionDate', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'label' => 'Date de la mise à jour'
            ])
            ->add('label', TextType::class, [
                'label' => 'Libellé de la mise à jour',
                'attr' => [
                    'placeholder' => 'Ex: approvisionnement du 01/01/2023'
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité à mettre à jour',
                'help' => 'Saisissez un nombre positif pour ajouter du stock, et un nombre négatif pour enlever du stock'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductStockTransaction::class,
        ]);
    }
}
