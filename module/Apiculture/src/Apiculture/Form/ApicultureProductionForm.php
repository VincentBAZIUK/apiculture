<?php
/**
 * Created by PhpStorm.
 * User: vincentbaziuk
 * Date: 27/03/14
 * Time: 13:17
 */

namespace Apiculture\Form;

use Zend\Form\Form;

class  ApicultureProductionForm extends Form

{
    public function __construct($name = null)
    {
        parent::__construct('');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Entrer la date de production',
                'required' => 'required',
                'id' => 'datepicker'
            ),
            'options' => array(
                'label' => 'Date',
            ),
        ));

        $this->add(array(
            'name' => 'production',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'placeholder' => 'Entrer le poids de la production',
                'required' => 'required',
                'class' => 'poids_production'
            ),
            'options' => array(
                'label' => 'Masse de la production',
            ),
        ));

        $this->add(array(
            'name' => 'id_hive',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'submitAddProduction',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn-info btn',
                'value' => 'Ajouter',
            )
        ));
    }
}
