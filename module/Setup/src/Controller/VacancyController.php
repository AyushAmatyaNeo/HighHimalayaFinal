<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Helper\ACLHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Form\VacancyForm;
use Setup\Model\Vacancy;
use Setup\Repository\VacancyRepository;
use Notification\Repository\EmailTemplateRepo;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class VacancyController extends HrisController {


    public function __construct(AdapterInterface $adapter, StorageInterface $storage) {
        parent::__construct($adapter, $storage);
        $this->initializeRepository(VacancyRepository::class);
        $this->initializeForm(VacancyForm::class);
     
    }


 public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();
              //  echo '<pre>';print_r($result);die;
                $vacancyTypeList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $vacancyTypeList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl]);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postedData = $request->getPost();
            //print_r($postedData);die;
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $vacancy = new Vacancy();
                $vacancy->exchangeArrayFromForm($this->form->getData());
				$vacancy->description=$postedData['description'];
                $vacancy->createdDt = Helper::getcurrentExpressionDate();
                $vacancy->createdBy = $this->employeeId;
                $vacancy->vacancyId = ((int) Helper::getMaxId($this->adapter, Vacancy::TABLE_NAME, Vacancy::VACANCY_ID)) + 1;
                $vacancy->status = 'E';
                $this->repository->add($vacancy);
                $this->flashmessenger()->addMessage("Vacancy Successfully added.");
                return $this->redirect()->toRoute("vacancy");
            } 
        }

return new ViewModel(Helper::addFlashMessagesToArray($this, [
                    'form' => $this->form,
                    
                    'messages' => $this->flashmessenger()->getMessages()
                   
                        ]
                )
        );
    }

    
    public function editAction() {


        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('vacancy');
        }
        $request = $this->getRequest();
         

        $vacancy = new Vacancy();
        if ($request->isPost()) {
            $data=$request->getPost();

         // echo '<pre>';print_r($data);die;
            $this->form->setData($request->getPost());
            // print_r('asfd');die;
            if ($this->form->isValid()) {
                $vacancy->exchangeArrayFromForm($this->form->getData());
                $vacancy->vacancyId = $id;
                $vacancy->description=$data['description'];
                $vacancy->vacancyStatus = $data['vacancy_status'];
                $vacancy->modifiedDt = Helper::getcurrentExpressionDate();
                $vacancy->modifiedBy = $this->employeeId;
                //  echo '<pre>';print_r($vacancy);die;
               
                $this->repository->edit($vacancy, $id);

                

                $this->flashmessenger()->addMessage("Vacancy Successfully Updated!!!");
                return $this->redirect()->toRoute("vacancy");
            }
        }
        $fetchData = $this->repository->fetchById($id)->getArrayCopy();
       //echo '<pre>'; print_r($fetchData);die;
        $vacancy->exchangeArrayFromDB($fetchData);
      
        $this->form->bind($vacancy);
        
          return [
            'form' => $this->form,
            'id' => $id,
            'vacancy' => $vacancy,
        
            'customRender' => Helper::renderCustomView(),
        ];
    }
    
    public function deleteAction() {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage("Vacancy Successfully Deleted!!!");
        return $this->redirect()->toRoute('vacancy');
    }


}

?>