<?php
namespace Apiculture\Form;

use Zend\Form\Form;

class  ApicultureAddForm extends Form

{
    public function __construct($name = null)
    {
        parent::__construct('');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Enter the hive name',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'latitude',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Enter the hive latitude',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Latitude',
            ),
        ));

        $this->add(array(
            'name' => 'longitude',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Enter the longitude',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Longitude',
            ),
        ));
        $this->add(array(
            'name' => 'submitAddHive',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn-info btn',
                'value' => 'Enregistrer',
            )
        ));
    }
}
