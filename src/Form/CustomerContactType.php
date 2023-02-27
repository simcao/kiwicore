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

use App\Entity\CustomerContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom du contact',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Martin'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom du contact',
                'required' => true,
                'attr' => [
                    'placeholder' => 'DURAND'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email du contact',
                'required' => false,
                'attr' => [
                    'placeholder' => 'martin.durand@exemple.fr'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone du contact',
                'required' => false,
                'attr' => [
                    'placeholder' => '0123456789'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerContact::class,
        ]);
    }
}
