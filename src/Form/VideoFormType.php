<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\Video;
use App\Entity\VideoType;
use App\Repository\VideoTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VideoFormType extends AbstractType
{
    private VideoTypeRepository $videoTypeRepository;
    public function __construct(VideoTypeRepository $videoTypeRepository)
    {
        $this->videoTypeRepository = $videoTypeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = $this->setTypes();
        $builder
            ->add('name')
            ->add('url')
            ->add('videoType', ChoiceType::class, [
                'choices' => $types,
                'choice_value' => 'name',
                'choice_label' => function(?VideoType $type) {
                    return $type ? $type->getName() : '';
                },
            ])
        ;
    }

    public function setTypes(): array
    {
        return $this->videoTypeRepository->findAll();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
