<?php
namespace Blog\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class CommentFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'comment',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
            ),
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),

        ));
    }
}