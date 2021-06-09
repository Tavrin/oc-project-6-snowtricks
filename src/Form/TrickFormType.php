<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Enum\GroupEnum;
use App\Repository\GroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickFormType extends AbstractType
{
    private GroupRepository $groupRepository;
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groups = $this->setGroups();
        $builder
            ->add('name')
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
                    'data-target-modal' => 'mediaModal'
                    ],
            ]);
        ;
    }

    public function setGroups(): array
    {
        return $this->groupRepository->findAll();
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
