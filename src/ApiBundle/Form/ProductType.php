<?php

namespace ApiBundle\Form;

use ApiBundle\Form\DataTransformer\FileToBase64Transformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('reference', TextType::class)
            ->add('quantity', IntegerType::class)
            ->add('description', TextType::class)
            ->add('picture1', TextType::class)
            ->add('picture2', TextType::class)
            ->add('picture3', TextType::class)
            ->add('picture4', TextType::class)
            ->add('picture5', TextType::class)
            ->add('pdf', TextType::class)
            ->add('zip', TextType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
            ->add('stock', EntityType::class, [
                'class' => 'ApiBundle:Stock'
            ]);

        $builder->get('picture1')->addModelTransformer(new FileToBase64Transformer());
        $builder->get('picture2')->addModelTransformer(new FileToBase64Transformer());
        $builder->get('picture3')->addModelTransformer(new FileToBase64Transformer());
        $builder->get('picture4')->addModelTransformer(new FileToBase64Transformer());
        $builder->get('picture5')->addModelTransformer(new FileToBase64Transformer());
        $builder->get('pdf')->addModelTransformer(new FileToBase64Transformer());
        $builder->get('zip')->addModelTransformer(new FileToBase64Transformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ApiBundle\Entity\Product',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'apibundle_product';
    }


}
