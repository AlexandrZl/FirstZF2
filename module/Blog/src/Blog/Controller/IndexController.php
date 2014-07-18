<?php
namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Blog\Form\BlogForm;
use Blog\Form\CommentForm;
use Blog\Entity;
use Blog\Form\CommentFilter;
use Zend\View\Model\JsonModel;


class IndexController extends AbstractActionController
{


    public function testAction()
    {
        ini_set('display_errors',1);
        $message = $this->params()->fromRoute('message' , 'MESSAGE FROM ROUTE');

        return new ViewModel(array('message' => $message));
    }

    public function addAction()
    {
        $form = new BlogForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $post = new \Blog\Entity\BlogPost();
                $post->exchangeArray($form->getData());
                $objectManager->persist($post);
                $objectManager->flush();
                $message = 'Blogpost succesfully saved!';
                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute('blog');
            } else {
                $message = 'Error while saving blogpost';
                $this->flashMessenger()->addErrorMessage($message);
            }
        }

        return array('form' => $form);
    }

    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $posts = $objectManager
            ->getRepository('\Blog\Entity\BlogPost')
            ->findBy(array());

        $posts_array = array();
        foreach ($posts as $post) {
            $posts_array[] = $post->getArrayCopy();
        }

        $view = new ViewModel(array('posts' => $posts_array,));
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {
            $auth_user = $auth->getIdentity();
        }
        return $view;
    }

    
    public function viewAction()
    {
        $form = new CommentForm();
        $form->get('submit')->setValue('Add Comment');
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Blogpost id doesn\'t set');
            return $this->redirect()->toRoute('blog');
        }
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $post = $objectManager
            ->getRepository('\Blog\Entity\BlogPost')
            ->findOneBy(array('id' => $id));

        $comments = $objectManager
            ->getRepository('\Blog\Entity\Comment')
            ->findBy(array('userId' => $id));

        $comments_array = array();
        foreach ($comments as $comment) {
            $comments_array[] = $comment->getArrayCopy();
        }
        if (!$post) {
            $this->flashMessenger()->addErrorMessage(sprintf('Blogpost with id %s doesn\'t exists', $id));
            return $this->redirect()->toRoute('blog');
        }

        $view = new ViewModel(array(
            'post' => $post->getArrayCopy(),
            'comments' => $comments_array,
            'form' => $form,
            'id_comment' => $id,
        ));

        return $view;

       
    }


    public function editAction()
    {
        $form = new \Blog\Form\BlogForm();
        $form->get('submit')->setValue('Save');

        $request = $this->getRequest();
        if (!$request->isPost()) {
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                $this->flashMessenger()->addErrorMessage('Blogpost id doesn\'t set');
                return $this->redirect()->toRoute('blog');
            }

            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $post = $objectManager
                ->getRepository('\Blog\Entity\BlogPost')
                ->findOneBy(array('id' => $id));

            if (!$post) {
                $this->flashMessenger()->addErrorMessage(sprintf('Blogpost with id %s doesn\'t exists', $id));
                return $this->redirect()->toRoute('blog');
            }
            $form->bind($post);
            return array('form' => $form);
        } else {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

                $data = $form->getData();
                $id = $data['id'];
                try {
                    $blogpost = $objectManager->find('\Blog\Entity\BlogPost', $id);
                }
                catch (\Exception $ex) {
                    return $this->redirect()->toRoute('blog', array(
                        'action' => 'index'
                    ));
                }

                $blogpost->exchangeArray($form->getData());

                $objectManager->persist($blogpost);
                $objectManager->flush();

                $message = 'Blogpost succesfully saved!';
                $this->flashMessenger()->addMessage($message);

                return $this->redirect()->toRoute('blog');
            } else {
                $message = 'Error while saving blogpost';
                $this->flashMessenger()->addErrorMessage($message);
            }
        }
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Blogpost id doesn\'t set');
            return $this->redirect()->toRoute('blog');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                try {
                    $blogpost = $objectManager->find('Blog\Entity\BlogPost', $id);
                    $objectManager->remove($blogpost);
                    $objectManager->flush();
                }
                catch (\Exception $ex) {
                    $this->flashMessenger()->addErrorMessage('Error while deleting data');
                    return $this->redirect()->toRoute('blog', array(
                        'action' => 'index'
                    ));
                }

                $this->flashMessenger()->addMessage(sprintf('Blogpost %d was succesfully deleted', $id));
            }

            return $this->redirect()->toRoute('blog');
        }

        return array(
            'id'    => $id,
            'post' => $objectManager->find('Blog\Entity\BlogPost', $id)->getArrayCopy(),
        );
    }
}