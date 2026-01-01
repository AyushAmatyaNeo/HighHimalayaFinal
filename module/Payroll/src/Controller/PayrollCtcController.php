<?php

namespace Payroll\Controller;

use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Payroll\Form\CtcForm;
use Payroll\Model\PayrollCtc;
use Payroll\Repository\PayrollCtcRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PayrollCtcController extends AbstractActionController
{

    private $repository;
    private $form;
    private $requestForm;
    private $adapter;
    private $employeeId;
    private $storageData;
    private $acl;

    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        $this->adapter = $adapter;
        $this->repository = new PayrollCtcRepository($adapter);
        $this->storageData = $storage->read();
        $this->employeeId = $this->storageData['employee_id'];
        $this->acl = $this->storageData['acl'];
    }

    public function initializeForm()
    {
        $ctcform = new CtcForm();
        $builder = new AnnotationBuilder();
        if (!$this->form) {
            $this->form = $builder->createForm($ctcform);
        }
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $this->initializeForm();
        if ($request->isPost()) {
            try {
                $searchQuery = $request->getPost();
                // echo "<pre>"; print_r($searchQuery); die;
                $result = $this->repository->fetchPayrollDetails((array) $searchQuery);

                $payrollResult = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $payrollResult, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        $employee = $this->repository->fetchAllEmployee();
        $employees = [];
        foreach ($employee as $key => $value) {
            array_push($employees, ["id" => $key, "name" => $value]);
        }

        $fiscalyear = EntityHelper::getTableKVList($this->adapter, "HRIS_FISCAL_YEARS", "FISCAL_YEAR_ID", ["FISCAL_YEAR_NAME"], null, null, false, "FISCAL_YEAR_ID", "DESC");
        $selectedFiscalYearId = key($fiscalyear);
        $fiscalyears = [];
        foreach ($fiscalyear as $key => $value) {
            array_push($fiscalyears, ["id" => $key, "name" => $value]);
        }

        return Helper::addFlashMessagesToArray($this, [
            'acl' => $this->acl,
            'employees' => $employees,
            'fiscalYears' => $fiscalyears,
            'selectedFiscalYearId' => $selectedFiscalYearId,
            'form' => $this->form,
            'searchValues' => EntityHelper::getSearchData($this->adapter),
        ]);
    }

    public function addAction()
    {
        ACLHelper::checkFor(ACLHelper::ADD, $this->acl, $this);
        $this->initializeForm();
        $request = $this->getRequest();

        $flats = $this->repository->fetchFlatSetup();
        // echo "<pre>"; print_r($flats); die;

        if ($request->isPost()) {

            $this->form->setData($request->getPost());

            if ($this->form->isValid()) {
                $ctc = new PayrollCtc();
                $ctc->exchangeArrayFromForm($this->form->getData());
                $ctc->id = ((int) Helper::getMaxId($this->adapter, PayrollCtc::TABLE_NAME, PayrollCtc::ID)) + 1;
                $ctc->isDeleted = 'N';
                // echo "<pre>"; print_r($ctc); die;

                $this->repository->add($ctc);

                $this->flashmessenger()->addMessage("Payroll CTC setup Successfully added!!!");
                return $this->redirect()->toRoute('payroll-ctc', ['action' => 'setup']);
            }
        }

        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'customRenderer' => Helper::renderCustomView(),
                    'form' => $this->form,
                    'ratings' => $this->repository->fetchActiveRecord(),
                    'flats' => $flats,
                    'messages' => $this->flashmessenger()->getMessages()
                ]
            )
        );
    }

    public function editAction()
    {
        ACLHelper::checkFor(ACLHelper::UPDATE, $this->acl, $this);
        $id = (int)$this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('position');
        }

        $flats = $this->repository->fetchFlatSetup();
        $this->initializeForm();
        $request = $this->getRequest();
        $ctc = new PayrollCtc();

        if (!$request->isPost()) {
            // Fetch existing data for the main rating
            $ctc->exchangeArrayFromDB($this->repository->fetchById($id)->getArrayCopy());
            $this->form->bind($ctc);
        } else {
            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $ctc->exchangeArrayFromForm($this->form->getData());

                // Update the main rating record
                $this->repository->edit($ctc, $id);

                $this->flashmessenger()->addMessage("Payroll CTC setup Successfully Updated!!!");
                return $this->redirect()->toRoute('payroll-ctc', ['action' => 'setup']);
            }
        }

        return Helper::addFlashMessagesToArray(
            $this,
            [
                'customRenderer' => Helper::renderCustomView(),
                'form' => $this->form,
                'id' => $id,
                'flats' => $flats,
                'ctc' => $ctc ?? [], // Pass existing details for dynamic fields
            ]
        );
    }

    public function setupAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchActiveRecord();
                $positionList = Helper::extractDbData($result);
                return new CustomViewModel(['success' => true, 'data' => $positionList, 'error' => '']);
            } catch (Exception $e) {
                return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function deleteAction()
    {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        if (!$id) {
            return $this->redirect()->toRoute('payroll-ctc', ['action' => 'setup']);
        }
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Payroll CTC Setup Successfully Deleted!!!");
        return $this->redirect()->toRoute('payroll-ctc', ['action' => 'setup']);
    }
}
