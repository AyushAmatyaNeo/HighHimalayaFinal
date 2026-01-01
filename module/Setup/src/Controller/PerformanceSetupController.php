<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Application\Custom\CustomViewModel;
use Exception;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel; 
use Zend\View\Model\ViewModel;
use DateTime;
use Application\Helper\EntityHelper;
use Setup\Form\PerformanceSetupForm;
use Setup\Model\PerformanceSetup;
use Setup\Repository\PerformanceSetupRepository;

class PerformanceSetupController extends HrisController
{

    // private $repository;
    // private $form;
    // private $adapter;
    // private $employeeId;
    // private $storageData;
    // private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
       
        $this->initializeRepository(PerformanceSetupRepository::class);
        $this->initializeForm(PerformanceSetupForm::class);

    }

    public function indexAction()
    {
        $request = $this->getRequest();
        //echo '<pre>';print_r('rew');die;
         if ($request->isPost()) {
            try {
                $result = $this->repository->fetchGroupDetails();
                // echo '<pre>';print_r($result);die;

                $empList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $empList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }


    public function addAction() {
        //dd("hi");
        $request = $this->getRequest();

        if ($request->isPost()) {
            //echo'<pre>';print_r($request);die;
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            //echo'<pre>';print_r($postedData);die;
           if (!$this->form->isValid()) {
           //echo '<pre>';print_r('Without Validation');die;
                $performanceSetup = new PerformanceSetup();
                $performanceSetup->exchangeArrayFromForm($this->form->getData());
                $performanceSetup->performanceId = ((int) Helper::getMaxId($this->adapter, PerformanceSetup::TABLE_NAME, PerformanceSetup::PERFORMANCE_ID)) + 1;
                $performanceSetup->createdDate = Helper::getcurrentExpressionDate();
                $performanceSetup->createdBy = $this->employeeId;
                $performanceSetup->status = 'E';
                //echo'<pre>';print_r($performanceSetup);die;
                $this->repository->add($performanceSetup);
                //echo'<pre>';print_r($performanceSetup);die;
                $this->flashmessenger()->addMessage("Setup Successfully added.");
                return $this->redirect()->toRoute("performanceSetup");
            } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'customRenderer' => Helper::renderCustomView(),
                        ]
                )
        );
    }
    // private function prepareForm($id = null) {
        
    // }
    // public function editAction() {
    //     //echo'<pre>';print_r("hi");die;
    //     $id = (int) $this->params()->fromRoute("id");
    //     //echo'<pre>';print_r($id);die;

    //     if ($id === 0) {
    //         return $this->redirect()->toRoute('performanceSetup');
    //     }
    //     $request = $this->getRequest();
    //     $performanceSetup = new PerformanceSetup();
    //     //echo'<pre>';print_r($performanceSetup);die;
    //     $detail = $this->repository->fetchById($id)->getArrayCopy();

    //     $detail = $this->repository->fetchById($id);
    //     //echo'<pre>';print_r($detail);die;
    //     if ($request->isPost()) {
    //         //echo'<pre>';print_r($performanceSetup);die;
    //         $this->form->setData($request->getPost());
    //         if (!$this->form->isValid()) 
    //         {
    //             //echo'<pre>';print_r($performanceSetup);die;
    //             $performanceSetup->exchangeArrayFromForm($this->form->getData());
    //             $performanceSetup->modifiedDt = Helper::getcurrentExpressionDate();
    //             $performanceSetup->modifiedBy = $this->employeeId;
    //             //echo'<pre>';print_r($performanceSetup);die;
    //             $this->repository->edit($performanceSetup, $id);
    //             //echo'<pre>';print_r($performanceSetup);die;
    //             $this->flashmessenger()->addMessage("Performance Setup Successfully Updated!!!");
    //             return $this->redirect()->toRoute("performanceSetup");
    //         }
    //     }
    //     // $performanceSetup->exchangeArrayFromDb($detail);
    //     $this->form->bind($performanceSetup);
    //     $this->prepareForm($id);

    //     return ['form' => $this->form, 'id' => $id,
    // ];
    // }

    public function editAction() {
        //echo'<pre>';print_r("hi");die;
        $id = (int) $this->params()->fromRoute("id");
        //echo'<pre>';print_r($id);die;

        if ($id === 0) {
            return $this->redirect()->toRoute('performanceSetup');
        }
        $request = $this->getRequest();
        $performanceSetup = new PerformanceSetup();
        //echo'<pre>';print_r($performanceSetup);die;
        $detail = $this->repository->fetchById($id)->getArrayCopy();

        $detail = $this->repository->fetchById($id);
        //echo'<pre>';print_r($detail);die;
        if (!$request->isPost()) {
            $performanceSetup->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($performanceSetup);
        } else {
            $this->form->setData($request->getPost());
            if (!$this->form->isValid()) {
                // echo'<pre>';print_r("hi");die;
                $performanceSetup->exchangeArrayFromForm($this->form->getData());
                $performanceSetup->modifiedDate = Helper::getcurrentExpressionDate();
                $performanceSetup->modifiedBy = $this->employeeId;
                //echo'<pre>';print_r($performanceSetup);die;
                $this->repository->edit($performanceSetup, $id);
                //echo'<pre>';print_r($performanceSetup);die;
                $this->flashmessenger()->addMessage("Performance Setup Successfully Updated!!!");
                return $this->redirect()->toRoute("performanceSetup");
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'customRenderer' => Helper::renderCustomView(),
                'form' => $this->form,
                'id' => $id
            ]
        );
    }
    

    public function deleteAction() {
        // dd("hi");
        $id = (int) $this->params()->fromRoute("id");
        // echo'<pre>';print_r($id);die;
        if (!$id) {
            return $this->redirect()->toRoute('performanceSetup');
        }
        $this->repository->delete($id);
        // echo'<pre>';print_r($id);die;
        $this->flashmessenger()->addMessage("Setup Successfully Deleted!!!");
        return $this->redirect()->toRoute('performanceSetup');
    }
    // public function deleteAction()
    // {
    //     $id = (int) $this->params()->fromRoute("id");
    //     $performanceSetup = new PerformanceSetup();
    //     $performanceSetup->employeeId = $id;
    //     $performanceSetup->deletedBy = $this->employeeId;
    //     $performanceSetup->deletedDate = Helper::getcurrentExpressionDateTime();
    //     $this->repository->delete($performanceSetup);
    //     $this->flashmessenger()->addMessage("Setup Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('performanceSetup');
    // }

    
}




