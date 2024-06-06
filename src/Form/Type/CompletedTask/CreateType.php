<?php

namespace App\Form\Type\CompletedTask;

use App\Entity\Student;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

class CreateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'student',
                EntityType::class,
                [
                    'label' => 'Студент',
                    'class' => Student::class,
                    'choice_label' => static fn(Student $s) => sprintf('%s %s', $s->getFirstName(), $s->getLastName())
                ]
            )
            ->add(
                'task',
                EntityType::class,
                [
                    'label' => 'Задача',
                    'class' => Task::class,
                    'choice_label'=> 'name'
                ]
            )
            ->add(
                'grade',
                IntegerType::class,
                [
                    'label' => 'Оценка',
                    'constraints' => [
                        new Range(min: 1, max: 10)
                    ]
                ])
            ->add('submit', SubmitType::class, ['label' => 'Создать']);
    }
}
