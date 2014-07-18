<?php
namespace Blog\Form;

use Zend\Form\Form;

class AuthForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('login');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'email', 
            'attributes' => array(
                'type'  => 'email',
            ),
            'options' => array(
                'label' => 'Email',
                'min' => 3,
                'max' => 100
            ),
        ));
        $this->add(array(
            'name' => 'password', 
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        )); 
    }
}