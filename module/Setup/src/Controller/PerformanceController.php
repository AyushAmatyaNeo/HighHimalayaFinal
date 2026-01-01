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
use Setup\Form\PerformanceForm;
use Setup\Model\Performance;
use Setup\Repository\PerformanceRepository;

class PerformanceController extends HrisController
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
       
        $this->initializeRepository(PerformanceRepository::class);
        $this->initializeForm(PerformanceForm::class);

    }

    public function indexAction()
    {
        $request = $this->getRequest();
        //echo '<pre>';print_r('rew');die;
         if ($request->isPost()) {
            //echo '<pre>';print_r($request);die;
            try {
                $result = $this->repository->fetchGroupDetails();
                //echo '<pre>';print_r($result);die;
                $empList = Helper::extractDbData($result);
                //echo '<pre>';print_r($empList);die;
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
                $performance = new Performance();
                $performance->exchangeArrayFromForm($this->form->getData());
                $performance->indexId = ((int) Helper::getMaxId($this->adapter, Performance::TABLE_NAME, Performance::INDEX_ID)) + 1;
                $performance->createdDate = Helper::getcurrentExpressionDate();
                $performance->createdBy = $this->employeeId;
                $performance->status = 'E';
                // echo'<pre>';print_r($performanceSetup);die;
                $this->repository->add($performance);
                //echo'<pre>';print_r($performanceSetup);die;
                $this->flashmessenger()->addMessage("Setup Successfully added.");
                return $this->redirect()->toRoute("performance");
            } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    // 'HrisEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_ID", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE, $this->employeeId),
                    // 'DnmEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "hr_employee_setup", "EMPLOYEE_CODE", ["EMPLOYEE_CODE", "EMPLOYEE_NDESC"], ["EMPLOYEE_STATUS" => 'Working', 'DELETED_FLAG' => 'N', 'LOCK_FLAG' => "N"], "EMPLOYEE_NDESC", "ASC", "-", FALSE, TRUE, $this->employeeId),
                  
                    'customRenderer' => Helper::renderCustomView(),
                        ]
                )
        );
    }
   
    private function prepareForm($id = null) {
        
    }
    public function editAction() {
        //echo'<pre>';print_r("hi");die;
        $id = (int) $this->params()->fromRoute("id");
        //echo'<pre>';print_r($id);die;

        if ($id === 0) {
            return $this->redirect()->toRoute('performance');
        }
        $request = $this->getRequest();
        $performance = new Performance();
        //echo'<pre>';print_r($performance);die;
        $detail = $this->repository->fetchById($id)->getArrayCopy();

        $detail = $this->repository->fetchById($id);
        //echo'<pre>';print_r($detail);die;
        if (!$request->isPost()) {
            $performance->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($performance);
        } else {
            $this->form->setData($request->getPost());
            if (!$this->form->isValid()) {
                // echo'<pre>';print_r("hi");die;
                $performance->exchangeArrayFromForm($this->form->getData());
                $performance->modifiedDate = Helper::getcurrentExpressionDate();
                $performance->modifiedBy = $this->employeeId;
                // echo'<pre>';print_r($performanceSetup);die;
                $this->repository->edit($performance, $id);
                //echo'<pre>';print_r($performanceSetup);die;
                $this->flashmessenger()->addMessage("Performance Successfully Updated!!!");
                return $this->redirect()->toRoute("performance");
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
   

    // public function deleteAction() {
    //     //dd("hi");
    //     $id = (int) $this->params()->fromRoute("id");
    //     //echo'<pre>';print_r($id);die;
    //     if (!$id) {
    //         return $this->redirect()->toRoute('performance');
    //     }
    //     $this->repository->delete($id);
    //     // echo'<pre>';print_r($id);die;
    //     $this->flashmessenger()->addMessage("Setup Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('performance');
    // }
    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('performance');
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Performance Successfully Deleted!!!");
        return $this->redirect()->toRoute('performance');
    }

    // public function deleteAction() {
    //     // dd("hi");
    //     $id = (int) $this->params()->fromRoute("id");
    //     //echo'<pre>';print_r($id);die;
    //     if (!$id) {
    //         return $this->redirect()->toRoute('performance');
    //     }
    //     $this->repository->delete($id);
    //     //echo'<pre>';print_r($id);die;
    //     $this->flashmessenger()->addMessage("Performance Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('performance');
    // }

}




