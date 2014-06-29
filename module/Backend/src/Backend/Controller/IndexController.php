<?php

namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
    	$message = 'Your authentication credentials are not valid';
    	$this->flashMessenger()->addErrorMessage($message);
    	$test = "Test from index";
        return array('test' => $test);
    }

}