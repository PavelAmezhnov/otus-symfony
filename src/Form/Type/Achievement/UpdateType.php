<?php

namespace App\Form\Type\Achievement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Название',
                    'constraints' => [
                        new NotBlank(),
                        new Length(min: 3)
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Обновить'])
            ->setMethod('PATCH');
    }
}
