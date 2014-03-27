<?php
/**
 * Created by PhpStorm.
 * User: vincentbaziuk
 * Date: 27/03/14
 * Time: 13:17
 */

namespace Apiculture\Form;

use Zend\Form\Form;

class  ApicultureInterventionForm extends Form

{
    public function __construct($name = null)
    {
        parent::__construct('');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Entrer une description',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Date',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Date',
                'class' => 'datepicker',
            ),
        ));

        $this->add(array(
            'name' => 'submitAddIntervention',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn-info btn',
                'value' => 'Ajouter',
            )
        ));
    }
}
