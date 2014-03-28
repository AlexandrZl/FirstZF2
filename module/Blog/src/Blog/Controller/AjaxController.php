<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;    
use Zend\View\Model\JsonModel;
use Blog\Form\CommentForm;

class AjaxController extends AbstractActionController
{

    public function init()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        }
    }
    public function addcommentAction()
    {
        if ($this->request->isXmlHttpRequest()) 
        {   
            $comment = $_POST['data'];
            if(isset($comment))
            {
                $con=mysqli_connect("localhost","root","12345","blog");
                if (mysqli_connect_errno())
                  {
                  echo "Failed to connect to MySQL: " . mysqli_connect_error();
                  }

                mysqli_query($con,"INSERT INTO comment (comment, userId)
                VALUES ( '$comment', 10)");

                $result = mysqli_query($con,"SELECT * FROM comment");
                mysqli_close($con);  
            }

        }

        $result = new JsonModel(array(
        'test' => "Success send comment",
            'success'=>true,
        ));  
        return array(
            'result' => $result,
            );
    }
}