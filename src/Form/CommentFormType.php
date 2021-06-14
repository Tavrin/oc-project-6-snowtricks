<?php

namespace App\Form;

use App\Entity\Comment;
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

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextAreaType::class,  [
                'attr' => [
                    'cols' => '55',
                    'rows' => '5'
                ]
            ])
            ->add('parentId', HiddenType::class, [
                'mapped' => false,
                'data' => $options['parentId']
            ])
            ->add('trickId', HiddenType::class, [
                'mapped' => false,
                'data' => $options['trickId']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'parentId' => null,
            'trickId' => null
        ]);

        $resolver->setAllowedTypes('parentId', ['int', 'null']);
        $resolver->setAllowedTypes('trickId', ['int', 'null']);
    }
}
