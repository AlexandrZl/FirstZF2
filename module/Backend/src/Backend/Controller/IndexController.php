<?php

namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Backend\Form\CommentForm;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        ini_set('display_errors',1);
        $form = new CommentForm();
        $form->get('submit')->setValue('Test');
    	$message = 'Your authentication credentials are not valid';
    	    	$test = "Test from index";
        return array(
            'test' => $test,
            'form' => $form,
        );
    }

    public function testAction()
    {

        $result = new JsonModel(array(
            'response' => "test",
            'success'  => true,
        ));  

        return $result; 

    }

}