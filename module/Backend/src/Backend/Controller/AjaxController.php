<?php

namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Backend\Form\CommentForm;
use Zend\View\Model\JsonModel;

class AjaxController extends AbstractActionController
{

    public function testAction()
    {

        $result = new JsonModel(array(
            'response' => "test",
            'success'  => true,
        ));  

        return $result; 

    }

}