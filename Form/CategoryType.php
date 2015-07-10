<?php

namespace Core\Bundle\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CategoryType
 */
class CategoryType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('metaTitle')
            ->add('metaDescription')
            ->add('metaTags')   
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\BlogBundle\Entity\Category',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_blogbundle_categorytype';
    }
}
