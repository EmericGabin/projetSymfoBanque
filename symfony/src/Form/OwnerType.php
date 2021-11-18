<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Owner;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class OwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $owner = $builder->getData();
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('birthday', DateType::class, [
                'years' => range(date('Y') - 18, date('Y') - 80)
            ])
            ->add('phone_number')
            ->add('email')
            ->add('beneficiaries', EntityType::class, [
                'class' => Account::class,
                'choice_label' => 'iban',
                'multiple' => true,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($owner) {
                    if($owner->getId()){
                        return $er ->findAccountFromDiffOwner($owner);
                    }
                    return $er->createQueryBuilder('a');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Owner::class,
        ]);
    }
}
