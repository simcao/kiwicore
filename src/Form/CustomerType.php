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

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Simcao EI
 */
class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company', ChoiceType::class, [
                'label' => 'Typologie de client',
                'choices' => [
                    'Client entreprise' => true,
                    'Client particulier' => false,
                ],
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Raison sociale / Prénom NOM',
                'attr' => [
                    'placeholder' => 'ACME S.A.S.'
                ],
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse postale du client',
                'attr' => [
                    'placeholder' => '1 Avenue de la République'
                ],
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Paris'
                ],
                'required' => false
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => '75000'
                ],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email du client',
                'attr' => [
                    'placeholder' => 'contact@acme.fr'
                ],
                'help' => 'Utilisé pour les communications par défaut',
                'required' => false,
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone du client',
                'attr' => [
                    'placeholder' => '01.23.45.67.89'
                ],
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
