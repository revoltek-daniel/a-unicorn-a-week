<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImageType
 *
 * @package DanielBundle\Form\Type
 */
class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'active',
                CheckboxType::class,
                [
                    'label' => 'Aktiv',
                    'required' => false,
                ]
            )
            ->add(
                'activeFrom',
                DateTimeType::class,
                [
                    'label' => 'Aktiv ab',
                    'required' => false,
                ]
            )
            ->add('title');
        $this->addDescription($builder);
        $this->addImage($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addImage(FormBuilderInterface $builder): void
    {
        $builder->add(
            'image',
            FileType::class,
            array(
                'required' => false,
            )
        );
    }

    private function addDescription(FormBuilderInterface $builder): void
    {
        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'Alternativtext',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'cols' => 50,
                ],
            ]
        );
    }
}
