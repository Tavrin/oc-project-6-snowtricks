<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Manager\TrickManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class TrickFormType extends AbstractType
{
    private TrickManager $trickManager;

    public function __construct(TrickManager $trickManager)
    {

        $this->trickManager = $trickManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groups = $this->trickManager->setGroups();
        $builder
            ->add('name', TextType::class,
                [
                    'constraints' => [
                        new Length(['min' => 5, 'max' => 30]),
                    ],
                ])
            ->add('description', TextAreaType::class)
            ->add('trickGroup', ChoiceType::class, [
                'choices' => $groups,
                'choice_value' => 'name',
                'choice_label' => function(?Group $group) {
                    return $group ? $group->getName() : '';
                },
            ])
            ->add('mainMedia', HiddenType::class, [
                'mapped'=> false,
                'attr' => [
                'class' => 'js-binder',
                'id' => 'mainMedia',
                'data-type' => 'image',
                'data-from' => 'modal',
                'data-target' => 'previewImage'
                    ]
            ])
            ->add('addMedia', ButtonType::class, [
                'attr' => [
                    'class' => 'btn js-modal',
                    'data-target' => 'trick_form_mainMedia',
                    'data-target-id' => 'mediaModal',
                    'data-type' => 'image'
                    ],
                'label' => 'Images'
            ])
            ->add('videos', ButtonType::class, [
                'attr' => [
                    'class' => 'btn js-modal',
                    'data-target' => 'trick_form_mainMedia',
                    'data-target-id' => 'mediaModal',
                    'data-type' => 'video'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
