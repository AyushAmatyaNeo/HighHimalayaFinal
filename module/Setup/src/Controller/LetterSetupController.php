<?php

namespace Setup\Controller;

use Application\Controller\HrisController;
use Application\Custom\CustomViewModel;
use Application\Helper\ACLHelper;
use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Exception;
use Setup\Repository\LetterSetupDetailRepository;
use Setup\Form\LetterSetupForm;
use Setup\Model\HrEmployees;
use Setup\Model\LetterHead;
use Setup\Model\LetterSetup;
use Setup\Model\LetterSetupDetail;
use Setup\Repository\LetterHeadRepository;
use Setup\Repository\LetterSetupRepository;
use Setup\Repository\VariableRepository;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class LetterSetupController extends HrisController
{
    public $variables;
    public function __construct(AdapterInterface $adapter, StorageInterface $storage)
    {
        parent::__construct($adapter, $storage);
        $this->storageData = $storage->read();
        $this->initializeRepository(LetterSetupRepository::class);
        $this->variables = new VariableRepository($adapter);
        $this->initializeForm(LetterSetupForm::class);
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchAll();

                $letterList = Helper::extractDbData($result);

                return new JsonModel(['success' => true, 'data' => $letterList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }

        $imageData = $this->getFileInfo($this->adapter, 1);
        return Helper::addFlashMessagesToArray($this, ['acl' => $this->acl, 'imageData' => $imageData,]);
    }


    public function subLettterAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('letterSetup');
        }
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                $result = $this->repository->fetchSubLetterAll($id);
                $subletterList = Helper::extractDbData($result);
                return new JsonModel(['success' => true, 'data' => $subletterList, 'error' => '']);
            } catch (Exception $e) {
                return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
            }
        }
        return Helper::addFlashMessagesToArray(
            $this,
            [
                'acl' => $this->acl,
                'id' => $id,
            ]
        );
    }

    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postedData = $request->getPost(); 

            $this->form->setData($postedData);
            if ($this->form->isValid()) {

                $letterSetupDetailRepository = new LetterSetupDetailRepository($this->adapter);
                $letterSetup = new LetterSetup();
                $letterSetup->exchangeArrayFromForm($this->form->getData());
                $letterSetup->isCustom = $postedData['is_custom'] == true ? 'E' : 'D';
                $letterSetup->topPosition = $postedData['top'];
                $letterSetup->bottomPosition = $postedData['bottom'];
                $letterSetup->leftPosition = $postedData['left'];
                $letterSetup->rightPosition = $postedData['right'];
                $letterSetup->createdDate = Helper::getcurrentExpressionDate();
                $letterSetup->letterSetupId = ((int) Helper::getMaxId($this->adapter, LetterSetup::TABLE_NAME, LetterSetup::LETTER_SETUP_ID)) + 1;
                $this->repository->add($letterSetup);

                $letterSetupId = $letterSetup->letterSetupId;

                $descriptions = [];
                foreach ($postedData as $key => $value) { 
                    if (strpos($key, 'description_') === 0) {
                        $descriptions[] = $value;
                    }
                }

                foreach ($descriptions as $description) {
                    $letterSetupDetail = new LetterSetupDetail();
                    $letterSetupDetail->letterSetupId = $letterSetupId;
                    $letterSetupDetail->description = mb_convert_encoding($description, 'UTF-8', 'auto');;
                    $letterSetupDetail->letterSetupDetailId = ((int) Helper::getMaxId($this->adapter, LetterSetupDetail::TABLE_NAME, LetterSetupDetail::LETTER_SETUP_DETAIL_ID)) + 1;
                    $letterSetupDetailRepository->add($letterSetupDetail);
                }


                $this->flashmessenger()->addMessage("Letter Format Successfully added.");
                return $this->redirect()->toRoute("letterSetup");
            }
        }

        $image = $this->getFileInfo($this->adapter, 1);
        $company_logo = $image['fileName'];

        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'company_logo' => $company_logo,
                    'variables' => $this->variables->getVariables(),
                ]
            )
        );
    }


    public function addSubAction()
    {

        $id = (int) $this->params()->fromRoute('id');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postedData = $request->getPost();
            $this->form->setData($postedData);
            if ($this->form->isValid()) {
                $letterSetupDetailRepository = new LetterSetupDetailRepository($this->adapter);
                $letterSetup = new LetterSetup();
                $letterSetup->exchangeArrayFromForm($this->form->getData());
                $letterSetup->isCustom = $postedData['is_custom'] == true ? 'E' : 'D';
                $letterSetup->topPosition = $postedData['top'];
                $letterSetup->bottomPosition = $postedData['bottom'];
                $letterSetup->leftPosition = $postedData['left'];
                $letterSetup->rightPosition = $postedData['right'];
                $letterSetup->parent_id = $id;
                $letterSetup->createdDate = Helper::getcurrentExpressionDate();
                $letterSetup->letterSetupId = ((int) Helper::getMaxId($this->adapter, LetterSetup::TABLE_NAME, LetterSetup::LETTER_SETUP_ID)) + 1;
                $this->repository->add($letterSetup);

                $letterSetupId = $letterSetup->letterSetupId;

                $descriptions = [];
                foreach ($postedData as $key => $value) {
                    if (strpos($key, 'description_') === 0) {
                        $descriptions[] = $value;
                    }
                }

                foreach ($descriptions as $description) {
                    $letterSetupDetail = new LetterSetupDetail();
                    $letterSetupDetail->letterSetupId = $letterSetupId;
                    $letterSetupDetail->description = mb_convert_encoding($description, 'UTF-8', 'auto');;
                    $letterSetupDetail->letterSetupDetailId = ((int) Helper::getMaxId($this->adapter, LetterSetupDetail::TABLE_NAME, LetterSetupDetail::LETTER_SETUP_DETAIL_ID)) + 1;
                    $letterSetupDetailRepository->add($letterSetupDetail);
                }

                $this->flashmessenger()->addMessage("SubLetter Format Successfully added.");
                return $this->redirect()->toRoute('letterSetup', array(
                    'controller' => 'LetterSetup',
                    'action' =>  'subLettter',
                    'id' => $id
                ));
            }
        }

        $image = $this->getFileInfo($this->adapter, 1);
        $company_logo = $image['fileName'];
        return new ViewModel(
            Helper::addFlashMessagesToArray(
                $this,
                [
                    'form' => $this->form,
                    'messages' => $this->flashmessenger()->getMessages(),
                    'company_logo' => $company_logo,
                    'id' => $id,
                    'variables' => $this->variables->getVariables(),
                ]
            )
        );
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute("id");
        if ($id === 0) {
            return $this->redirect()->toRoute('letterSetup');
        }
        $request = $this->getRequest();

        $letterSetup = new LetterSetup();
        if ($request->isPost()) {
            $data = $request->getPost();

            $this->form->setData($request->getPost());
            if ($this->form->isValid()) {
                $letterSetup->exchangeArrayFromForm($this->form->getData());
                $letterSetup->letterSetupId = $id;
                $letterSetup->isCustom = $data['is_custom'] == true ? 'E' : 'D';
                $letterSetup->topPosition = $data['top'];
                $letterSetup->bottomPosition = $data['bottom'];
                $letterSetup->leftPosition = $data['left'];
                $letterSetup->rightPosition = $data['right'];
                $letterSetup->createdDate = Helper::getcurrentExpressionDate();
                $this->repository->edit($letterSetup, $id);
                $this->repository->deleteDetails($id);
                $letterSetupId = $letterSetup->letterSetupId;

                $letterSetupDetailRepository = new LetterSetupDetailRepository($this->adapter);

                $descriptions = [];
                foreach ($data as $key => $value) {
                    if (strpos($key, 'description_') === 0) {
                        $descriptions[] = $value;
                    }
                }

                foreach ($descriptions as $description) {
                    $letterSetupDetail = new LetterSetupDetail();
                    $letterSetupDetail->letterSetupId = $letterSetupId;
                    $letterSetupDetail->description = mb_convert_encoding($description, 'UTF-8', 'auto');
                    $letterSetupDetail->letterSetupDetailId = ((int) Helper::getMaxId($this->adapter, LetterSetupDetail::TABLE_NAME, LetterSetupDetail::LETTER_SETUP_DETAIL_ID)) + 1;
                    $letterSetupDetailRepository->add($letterSetupDetail);
                }

                $this->flashmessenger()->addMessage("Format Successfully Updated!!!");
                return $this->redirect()->toRoute("letterSetup");
            }
        }
        $fetchData = $this->repository->fetchById($id); 
        // dd($fetchData);
        $letterSetup->exchangeArrayFromDB($fetchData);
        $result = $this->repository->fetchSubLetterAll($id);

        $childCollections = [];
        foreach ($result as $row) {
            $letterSetupId = $row['SUB_LETTER_SETUP_ID'];

            if (!isset($childCollections[$letterSetupId])) {
                $childCollections[$letterSetupId] = [
                    'SUB_LETTER_SETUP_ID' => $row['SUB_LETTER_SETUP_ID'],
                    'LETTER_TITLE' => $row['LETTER_TITLE'],
                ];
            }
        }

        $this->form->bind($letterSetup);
        $image = $this->getFileInfo($this->adapter, 1);
        $company_logo = $image['fileName'];
        return [
            'form' => $this->form,
            'id' => $id,
            'letterSetup' => $letterSetup,
            'descriptions' => $fetchData['DESCRIPTIONS'],
            'company_logo' => $company_logo,
            'childs' => $childCollections,
            'customRender' => Helper::renderCustomView(),
            'variables' => $this->variables->getVariables(),

        ];
    }
    public function deleteAction()
    {
        if (!ACLHelper::checkFor(ACLHelper::DELETE, $this->acl, $this)) {
            return;
        };
        $id = (int) $this->params()->fromRoute("id");
        $this->repository->delete($id);
        $this->flashmessenger()->addMessage(" Successfully Deleted!!!");
        return $this->redirect()->toRoute('letterSetup');
    }

    public function assignAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                $data = $request->getPost();
                $letterSetupId = $data['letterId'];
                $childLetterIds = $data['childLetterIds'];

                if (!is_array($childLetterIds)) {
                    $childLetterIds = [];
                }
                $mergedLetterIds = array_filter(
                    array_merge([$letterSetupId], $childLetterIds),
                    function ($value) {
                        return !empty($value);
                    }
                );
                $employeeIds = $data['employeeIds'];

                $this->repository->assignLetter($mergedLetterIds, $employeeIds);
                return new JsonModel(['success' => true, 'error' => '']);
            } catch (\Exception $e) {
                return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        $letterList = EntityHelper::getTableKVListWithSortOption($this->adapter, LetterSetup::TABLE_NAME, LetterSetup::LETTER_SETUP_ID, [LetterSetup::LETTER_TITLE], [LetterSetup::STATUS => 'E'], LetterSetup::LETTER_SETUP_ID, "ASC", null, true);
        $employeeLists = EntityHelper::getTableList($this->adapter, HrEmployees::TABLE_NAME, [HrEmployees::EMPLOYEE_ID, HrEmployees::FULL_NAME], [HrEmployees::STATUS => EntityHelper::STATUS_ENABLED]);

        return new ViewModel([
            'searchValues' => EntityHelper::getSearchData($this->adapter),
            'acl' => $this->acl,
            'letter_lists' => $letterList,
            'employees' => $employeeLists
        ]);
    }

    public function fileUploadAction()
    {

        try {
            $request = $this->getRequest();
            $files = $request->getFiles()->toArray();
            if (sizeof($files) > 0) {
                $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($files['file']['name'], PATHINFO_FILENAME);
                $unique = Helper::generateUniqueName();
                $newFileName = $unique . "." . $ext;
                $success = move_uploaded_file($files['file']['tmp_name'], Helper::UPLOAD_DIR . "/" . $newFileName);
                if ($success) {
                    $fileRepository = new LetterHeadRepository($this->adapter);
                    $fileDetail = $fileRepository->fetchById(1);
                    if ($fileDetail == null) {
                        $fileRepository->createLetterHead();
                    }
                    $file = new LetterHead();
                    $file->fileCode = 1;
                    $file->filePath = $newFileName;
                    $file->fileName = $fileName . "." . $ext;
                    $file->createdDt = Helper::getcurrentExpressionDate();
                    $fileRepository->edit($file, 1);
                    return new CustomViewModel(['success' => true, 'data' => ["fileName" => $newFileName, "oldFileName" => $fileName . "." . $ext, 'fileCode' => $file->fileCode], 'error' => '']);
                } else {
                    throw new Exception("Moving uploaded file failed");
                }
            } else {
                throw new Exception("No file is uploaded");
            }
        } catch (Exception $e) {
            return new CustomViewModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function childListAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $childList = $this->repository->fetchChildLits($data['letterId']);
            return new JsonModel(['success' => true, 'data' => $childList, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }


    private function getFileInfo(AdapterInterface $adapter, $fileId)
    {
        $fileRepo = new LetterHeadRepository($adapter);
        $fileDetail = $fileRepo->fetchById($fileId);

        if ($fileDetail == null) {
            $imageData = [
                'fileCode' => null,
                'fileName' => null,
                'oldFileName' => null
            ];
        } else {
            $imageData = [
                'fileCode' => $fileDetail['FILE_CODE'],
                'oldFileName' => $fileDetail['FILE_NAME'],
                'fileName' => $fileDetail['FILE_PATH']
            ];
        }
        return $imageData;
    }


    public function viewEmpListAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $empList = $this->repository->fetchEmployeeList($data['empIds']);
            // dd($empList);
            return new JsonModel(['success' => true, 'data' => $empList, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

    public function empLettersAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new Exception("The request should be of type post");
            }
            $data = $request->getPost();
            $letterList = $this->repository->fetchEmployeeLetterList($data['empId']);
            return new JsonModel(['success' => true, 'data' => $letterList, 'error' => '']);
        } catch (Exception $e) {
            return new JsonModel(['success' => false, 'data' => [], 'error' => $e->getMessage()]);
        }
    }
}
