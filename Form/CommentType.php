<?php

namespace Core\Bundle\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommentType
 */
class CommentType extends AbstractType
{
    protected $formConfig;

    public function __construct($formConfig=array())
    {
        $this->formConfig = $formConfig;
    }
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($this->formConfig['projectIds'])) {
            $projectIds = $this->formConfig['projectIds'];
            $builder->add('post', 'entity', array(
                'class' => 'BlogBundle:Post',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($projectIds) {
                                        return $er->createQueryBuilder('a')
                                                ->select('a')
                                                ->join('a.project', 'p')
                                                ->where('p.id IN(:projectIds)')
                                                ->setParameter('projectIds', array_values($projectIds))
                                                ;

                },
                'required' => true,
                'empty_value'  => 'Seleccionar publicacion'
            ));
        }
        
        $builder
            ->add('actor')
            ->add('comment')
            ->add('isActive', null, array('required' => false))
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\BlogBundle\Entity\Comment',
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
