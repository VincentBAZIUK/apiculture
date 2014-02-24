<?php

namespace Apiculture\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class UpdateForm extends Form 
{
    public function __construct(ObjectManager $om) 
    {
        parent::__construct();
        
        
        
        $this->setName('updtae');
        $this->setAttribute('method','post');
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'text',
                )
        ))
                ->add(array(
                    'name'=>'submit',
                    'attributes'=>array(
                        'type'=>'submit',
                        'value'=>'Valider form'
                    )
                ));
    }
}