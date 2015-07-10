<?php

namespace Core\Bundle\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    protected $formConfig;

    public function __construct($formConfig=array())
    {
        $this->formConfig = $formConfig;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         
        $projectConf = array(
                'class' => 'CoreBundle:Project',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                                        return $er->createQueryBuilder('a')
                                                ->select('a')
                                                ;

                },
                'required' => true,
                'empty_value'  => 'Seleccionar proyecto'
            );
                
        if (isset($this->formConfig['projectIds'])) {
            $projectIds = $this->formConfig['projectIds'];
            $projectConf = array(
                'class' => 'CoreBundle:Project',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($projectIds) {
                                        return $er->createQueryBuilder('a')
                                                ->select('a')
                                                ->where('a.id IN(:projectIds)')
                                                ->setParameter('projectIds', array_values($projectIds))
                                                ;

                },
                'required' => true,
                'empty_value'  => 'Seleccionar proyecto'
            );
        }
        
        $builder
            ->add('title')
            ->add('project', 'entity', $projectConf)
            ->add('categories', 'entity', array(
                'property' => 'name', // Assuming that the entity has a "name" property
                'class'    => 'Core\Bundle\BlogBundle\Entity\Category',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                                        return $er->createQueryBuilder('u')
                                            ->where('u.isActive = 1')
                                            ->orderBy('u.name', 'ASC');
                                    },
                'multiple'  => true,
                'expanded' => false
             ))
            ->add('description', 'textarea', array())
            ->add('slug', 'text', array('required' => false))
            ->add('published', 'datetime', array(
                    'label' => 'Fecha de publicación',
                    'format' => 'dd/MM/yyyy',
                    'widget' => 'single_text',
                    'required' => false
                )
            )
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\BlogBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_bundle_blogbundle_post';
    }
}
