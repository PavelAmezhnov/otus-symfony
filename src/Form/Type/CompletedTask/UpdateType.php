<?php

namespace App\Form\Type\CompletedTask;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\When;

class UpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'grade',
                IntegerType::class,
                [
                    'label' => 'Оценка',
                    'constraints' => [
                        new When(expression: "this !== null", constraints: [new Range(min: 1, max: 10)])
                    ]
                ])
            ->add('submit', SubmitType::class, ['label' => 'Изменить'])
            ->setMethod('PATCH');
    }
}
