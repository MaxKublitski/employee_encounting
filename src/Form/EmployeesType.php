<?php

namespace App\Form;

use App\Entity\Employees;
use App\Entity\Organizations;
use App\Form\OrganizationsType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname')
            ->add('middlename')
            ->add('lastname')
            ->add('date_of_birth')
            ->add('inn')
            ->add('snils')
//            ->add('organization', EntityType::class, [
//                'multiple' => true,
//                'class'    => OrganizationsType::class,
//                'mapped' => true
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employees::class,
        ]);
    }
}
