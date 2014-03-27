<?php
namespace Blog\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comment');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'comment',
            'type' => 'Text',
            'options' => array(
                'label' => 'Comment',
            ),
            'attributes' => array(
                'id' => 'comment',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'submitbutton',
            ),
        ));
    }
}