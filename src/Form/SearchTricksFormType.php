<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Manager\TrickManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SearchTricksFormType extends AbstractType
{
    private TrickManager $trickManager;

    public function __construct(TrickManager $trickManager)
    {
        $this->trickManager = $trickManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groups = $this->trickManager->setGroups();
        $builder
            ->add('name', TextType::class, [
                'mapped' => false
            ])
            ->add('trickGroup', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'choices' => $groups,
                'choice_value' => 'id',
                'choice_label' => function(?Group $group) {
                    return $group ? $group->getName() : '';
                },
                'placeholder' => 'Tous les groupes',
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);    }
}
