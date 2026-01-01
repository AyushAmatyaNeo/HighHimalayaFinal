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
use Setup\Form\EmployeeSetupForm;
use Setup\Model\EmployeeSetup;
use Setup\Repository\EmployeeRepository;
use Setup\Repository\EmployeeSetupRepository;

class EmployeeSetupController extends HrisController
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
       
        $this->initializeRepository(EmployeeSetupRepository::class);
        $this->initializeForm(EmployeeSetupForm::class);

    }

    public function indexAction()
    {
        $request = $this->getRequest();
         // echo '<pre>';print_r('rew');die;
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
            // dd("hi");
            $postedData = $request->getPost();
            $this->form->setData($postedData);
           // echo'<pre>';print_r($postedData);die;
           if (!$this->form->isValid()) {
            // echo '<pre>';
            // print_r($this->form->getMessages());
            // die;
        
           //echo '<pre>';print_r('Without Validation');die;
                $employeeSetup = new EmployeeSetup();
                $employeeSetup->exchangeArrayFromForm($this->form->getData());
                $employeeSetup->id = ((int) Helper::getMaxId($this->adapter, EmployeeSetup::TABLE_NAME, EmployeeSetup::ID)) + 1;
                $employeeSetup->createdDate = Helper::getcurrentExpressionDate();
                $employeeSetup->createdBy = $this->employeeId;
                $employeeSetup->status = 'E';
                //echo'<pre>';print_r($employeeSetup);die;
                $this->repository->add($employeeSetup);
                $this->flashmessenger()->addMessage("Setup Successfully added.");
                return $this->redirect()->toRoute("employeeSetup");
            } 
        }
        return new ViewModel(Helper::addFlashMessagesToArray(
                        $this, [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'HrisEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_ID", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE, $this->employeeId),
                    'DnmEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "hr_employee_setup", "EMPLOYEE_CODE", ["EMPLOYEE_CODE", "EMPLOYEE_NDESC"], ["EMPLOYEE_STATUS" => 'Working', 'DELETED_FLAG' => 'N', 'LOCK_FLAG' => "N"], "EMPLOYEE_NDESC", "ASC", "-", FALSE, TRUE, $this->employeeId),
                  
                    //'customRenderer' => Helper::renderCustomView(),
                        ]
                )
        );
    }
    // public function addAction() {
    //     $request = $this->getRequest();
    //     if ($request->isPost()) {
    //         $this->form->setData($request->getPost());
    //         if ($this->form->isValid()) {
            
    //         //echo'<pre>';print_r("hi");die;
    //             $employeeSetup = new EmployeeSetup();
    //             //echo '<pre>';print_r($employeeSetup);die;
    //             $employeeSetup->exchangeArrayFromForm($this->form->getData());
    //             // $employeeSetup->createdDt = Helper::getcurrentExpressionDate();
    //             // $employeeSetup->createdBy = $this->employeeId;
    //             // $employeeSetup->id = (int) Helper::getMaxId($this->adapter, EmployeeSetup::TABLE_NAME, EmployeeSetup::EMPLOYEE_ID) + 1;
    //             $employeeSetup->employeeId = ((int) Helper::getMaxId($this->adapter, "DOMMY_EMPLOYEES", "EMPLOYEE_ID")) + 1;
    //             // $employeeSetup->status = 'E';
    //             $this->repository->add($employeeSetup);

    //             $this->flashmessenger()->addMessage("Employee Setup Successfully added!!!");
    //             return $this->redirect()->toRoute("employeeSetup");
    //         }
    //     }
    //     // $this->prepareForm();
    //     return [
    //         'form' => $this->form,
    //         'getData' =>$this->form,
    //         'HrisEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_ID", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE, $this->employeeId),
    //         //'DNMEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "hr_employee_setup", "EMPLOYEE_CODE", ["EMPLOYEE_CODE", "EMPLOYEE_NDESC "], [], "EMPLOYEE_NDESC", "ASC", "-", FALSE, TRUE, $this->employeeId),
    //         'DnmEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "hr_employee_setup", "EMPLOYEE_CODE", ["EMPLOYEE_CODE", "EMPLOYEE_NDESC"], ["EMPLOYEE_STATUS" => 'Working', 'DELETED_FLAG' => 'N', 'LOCK_FLAG' => "N"], "EMPLOYEE_NDESC", "ASC", "-", FALSE, TRUE, $this->employeeId),
    //         'customRender' => Helper::renderCustomView()
    //     ];
    // }
    private function prepareForm($id = null) {
        
    }
    public function editAction() {
        // echo'<pre>';print_r("hi");die;
        $id = (int) $this->params()->fromRoute("id");
        //echo'<pre>';print_r($id);die;
        if ($id === 0) {
            return $this->redirect()->toRoute('employeeSetup');
        }
        $request = $this->getRequest();
        $employeeSetup = new EmployeeSetup();
        //echo'<pre>';print_r($employeeSetup);die;
        $detail = $this->repository->fetchById($id)->getArrayCopy();
        if ($request->isPost()) {
            //echo'<pre>';print_r($employeeSetup);die;
            $this->form->setData($request->getPost());
            if (!$this->form->isValid()) 
            {
                //echo'<pre>';print_r($employeeSetup);die;
                $employeeSetup->exchangeArrayFromForm($this->form->getData());
                $employeeSetup->modifiedDt = Helper::getcurrentExpressionDate();
                $employeeSetup->modifiedBy = $this->employeeId;
                //echo'<pre>';print_r($employeeSetup);die;
                $this->repository->edit($employeeSetup, $id);
                //echo'<pre>';print_r($employeeSetup);die;
                $this->flashmessenger()->addMessage("Employee Setup Successfully Updated!!!");
                return $this->redirect()->toRoute("employeeSetup");
            }
        }
        $employeeSetup->exchangeArrayFromDb($detail);
        $this->form->bind($employeeSetup);
        $this->prepareForm($id);

        return ['form' => $this->form, 'id' => $id,
        'HrisEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_ID", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE, $this->employeeId),
        'DnmEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "hr_employee_setup", "EMPLOYEE_CODE", ["EMPLOYEE_CODE", "EMPLOYEE_NDESC"], ["EMPLOYEE_STATUS" => 'Working', 'DELETED_FLAG' => 'N', 'LOCK_FLAG' => "N"], "EMPLOYEE_NDESC", "ASC", "-", FALSE, TRUE, $this->employeeId)
    ];
    }
    // public function editAction()
    // {
    //     //echo '<pre>';print_r('yo');die;
    //     ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
    //     $id = (int) $this->params()->fromRoute("id");
        
    //     if ($id === 0) {
            
    //         return $this->redirect()->toRoute('employeeSetup');
    //     }

    //     //$this->initializeForm();
    //     $request = $this->getRequest();
    //     //echo '<pre>';print_r('yo');die;
    //     // $group = new SalarySheetGroup();
    //     $employeeSetup = new EmployeeSetup();
    //     if (!$request->isPost()) {
    //         //echo '<pre>';print_r('yo');die;
    //         //echo '<pre>';print_r($employeeSetup);die;
    //         //echo '<pre>';print_r($this->repository->fetchById($id));die;
    //         $employeeSetup->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
    //         $this->form->bind($employeeSetup);
            
    //     } else {
    //         //echo '<pre>';print_r($employeeSetup);die;
    //         $this->form->setData($request->getPost());
    //          if (!$this->form->isValid()) 
    //         {
    //             //echo'<pre>';print_r($employeeSetup);die;
    //             //echo '<pre>';print_r('yo');die;


    //             // $employeeSetup->exchangeArrayFromForm($this->form->getData());

    //             $employeeData = (array) $this->form->getData(); 
    //             $employeeSetup->exchangeArrayFromForm($employeeData);


    //             //echo'<pre>';print_r($employeeSetup);die;
    //             $employeeSetup->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
    //             //echo'<pre>';print_r($employeeSetup);die;
    //             $employeeSetup->createdBy = $this->employeeId;
    //             $employeeSetup->modifiedBy = $this->employeeId;
    //             $currentDateTime = new DateTime();
    //             $employeeSetup->modifiedDate = $currentDateTime->format('d-M-y'); 
    //             //echo'<pre>';print_r($employeeSetup);die;
                
    //             $this->repository->edit($employeeSetup, $id);
    //             //echo'<pre>';print_r($employeeSetup);die;
    //             $this->flashmessenger()->addMessage("Employee Setup Successfully Updated!!!");
    //             return $this->redirect()->toRoute("employeeSetup");
    //         }
    //     }
    //     //echo '<pre>';print_r('y');die;
    //     return Helper::addFlashMessagesToArray(
    //         $this,
    //         [
    //             'customRenderer' => Helper::renderCustomView(),
    //             'form' => $this->form,
    //             'HrisEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "HRIS_EMPLOYEES", "EMPLOYEE_ID", ["EMPLOYEE_ID", "FULL_NAME"], ["STATUS" => 'E', 'RETIRED_FLAG' => 'N', 'IS_ADMIN' => "N"], "FULL_NAME", "ASC", "-", FALSE, TRUE, $this->employeeId),
    //             'DnmEmployees' => EntityHelper::getTableKVListWithSortOption($this->adapter, "hr_employee_setup", "EMPLOYEE_CODE", ["EMPLOYEE_CODE", "EMPLOYEE_NDESC"], ["EMPLOYEE_STATUS" => 'Working', 'DELETED_FLAG' => 'N', 'LOCK_FLAG' => "N"], "EMPLOYEE_NDESC", "ASC", "-", FALSE, TRUE, $this->employeeId),
    //             'id' => $id
    //         ]
    //     );
    // }
    // public function deleteAction()
    // {
    //     if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
    //         return;
    //     };
    //     $id = (int) $this->params()->fromRoute("id");
    //     if (!$id) {
    //         return $this->redirect()->toRoute('Employee Setup');
    //     }
    //     // echo '<pre>';print_r($id);die;
    //     $this->repository->delete($id);
    //     $this->flashmessenger()->addMessage("Employee Setup Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('employeeSetup');
    // }

    public function deleteAction() {
        // dd("hi");
        $id = (int) $this->params()->fromRoute("id");
        //echo'<pre>';print_r($id);die;
        if (!$id) {
            return $this->redirect()->toRoute('employeeSetup');
        }
        $this->repository->delete($id);
       // echo'<pre>';print_r($id);die;
        $this->flashmessenger()->addMessage("Employee Setup Successfully Deleted!!!");
        return $this->redirect()->toRoute('employeeSetup');
    }

    // public function deleteAction()
    // {
    //     if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
    //         return;
    //     };
    //     $id = (int) $this->params()->fromRoute("id");
    //     //echo'<pre>';print_r($id);die;
    //     if (!$id) {
    //         return $this->redirect()->toRoute('employeeSetup');
    //     }
    //     // echo '<pre>';print_r($id);die;
    //     $this->repository->delete($id);
    //     //echo'<pre>';print_r($id);die;
    //     $this->flashmessenger()->addMessage("Employee Setup Successfully Deleted!!!");
    //     return $this->redirect()->toRoute('employeeSetup');
    // }
}




