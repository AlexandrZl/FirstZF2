<?php

namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
    	$message = 'Your authentication credentials are not valid';
    	    	$test = "Test from index";
        return array('test' => $test);
    }

    public function testAction()
    {
    	$test = "Test from test";
        $this->flashMessenger()->addErrorMessage($test);
        return array('test' => $test);
    }

}