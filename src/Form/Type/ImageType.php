<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $this->addDescription($builder);
        $this->addImage($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addImage(FormBuilderInterface $builder)
    {
        $builder->add(
            'image',
            FileType::class,
            array(
                'required' => false,
            )
        );
    }

    private function addDescription(FormBuilderInterface $builder)
    {
          $builder->add(
            'description',
            TextareaType::class,
            array(
                'required' => false,
            )
        );
    }
}
