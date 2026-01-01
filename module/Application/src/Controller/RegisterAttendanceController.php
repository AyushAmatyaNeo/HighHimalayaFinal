<?php

namespace Application\Controller;

use Application\Helper\Helper;
use Application\Model\HrisAuthStorage;
use Application\Model\Preference;
use Application\Model\User;
use Application\Model\UserLog;
use Application\Repository\UserLogRepository;
use AttendanceManagement\Model\Attendance;
use AttendanceManagement\Model\AttendanceDetail;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use AttendanceManagement\Repository\AttendanceRepository;
use DateTime;
use Exception;
use Notification\Controller\HeadNotification;
use Notification\Model\NotificationEvents;
use SelfService\Model\AttendanceRequestModel;
use SelfService\Repository\AttendanceRequestRepository;
use System\Repository\SystemSettingRepository;
use System\Repository\UserSetupRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Setup\Repository\EmployeeRepository;
use Application\Repository\MonthRepository;
use System\Repository\RolePermissionRepository;
use System\Repository\RoleSetupRepository;
use System\Repository\RoleControlRepository;

class RegisterAttendanceController extends AbstractActionController
{

    protected $form;
    protected $storage;
    protected $userName;
    protected $authservice;
    protected $adapter;
    protected $preference;

    public function __construct(AuthenticationService $authService, AdapterInterface $adapter)
    {
        $this->authservice = $authService;
        $this->storage = $authService->getStorage();
        $this->userName = $authService->getStorage()->read();
        $this->adapter = $adapter;
        $preferenceRepo = new SystemSettingRepository($adapter);
        $this->preference = new Preference();
        $this->preference->exchangeArrayFromDB($preferenceRepo->fetch());
        if (!($this->preference->allowSystemAttendance == 'Y')) {
            echo '!!!!!!!System Attendance is not Allowed';
            die();
        }
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/login');
        }, 100);
    }

    public function getForm()
    {
        if (!$this->form) {
            $user = new User();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($user);
        }

        return $this->form;
    }

    public function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()
                ->get('AuthService');
        }
        return $this->authservice;
    }

    public function getSessionStorage()
    {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get(HrisAuthStorage::class);
        }
        return $this->storage;
    }

    public function indexAction()
    {
        $form = $this->getForm();
        return Helper::addFlashMessagesToArray($this, [
            'form' => $form
        ]);
    }

    // public function authenticateAction()
    // {
    //     $request = $this->getRequest();
    //     $form = $this->getForm();
    //     $redirect = 'registerAttendance';
    //     if ($request->isPost()) {
    //         $postData = $request->getPost()->getArrayCopy();
    //         $form->setData($request->getPost());
    //         $storageData = $this->storage->read();
    //         $storage_userId = $storageData['user_id'];
    //         $sotrage_empID = $storageData['employee_id'];
    //         $sotrage_roleID = $storageData['employee_id'];
    //         if ($form->isValid()) {
    //             //check authentication...
    //             $this->getAuthService()->getAdapter()
    //                 ->setIdentity($request->getPost('username'))
    //                 ->setCredential($request->getPost('password'));
    //             $result = $this->getAuthService()->authenticate();
    //             foreach ($result->getMessages() as $message) {
    //                 $this->flashmessenger()->addMessage($message);
    //             }
    //             if ($result->isValid()) {
    //                 $redirect = 'login';
    //                 //after authentication success get the user specific details
    //                 $resultRow = $this->getAuthService()->getAdapter()->getResultRowObject();
    //                 $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
    //                 $employeeId = $resultRow->EMPLOYEE_ID;
    //                 $attendanceRepo = new AttendanceRepository($this->adapter);
    //                 $preference = new Preference();
    //                 if (isset($postData['checkInRemarks']) && ($preference->needApprovalForLateCheckIn == 'Y')) {
    //                     // echo '<pre>'; print_r("1"); die;
    //                     $this->attendanceRequest($postData, $employeeId);
    //                     $this->getAuthService()->clearIdentity();
    //                     return $this->redirect()->toRoute('login');
    //                 }
    //                 if (!isset($postData['checkInRemarks'])) {
    //                     // echo '<pre>'; print_r("2"); die;
    //                     $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
    //                     $inTime = $todayAttendance['IN_TIME'];
    //                     $halfDayFlag = $todayAttendance['HALFDAY_FLAG'];
    //                     $attendanceType = ($inTime) ? "OUT" : "IN";
    //                     $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
    //                     if (!$shiftDetails) {
    //                         $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift($employeeId);
    //                     }
    //                     $currentTimeDatabase = $shiftDetails['CURRENT_TIME'];
    //                     $checkInTimeDatabase = $shiftDetails['CHECKIN_TIME'];
    //                     $checkOutTimeDatabase = ($halfDayFlag == 'Y') ? $shiftDetails['HALF_DAY_CHECKOUT_TIME'] : $shiftDetails['CHECKOUT_TIME'];

    //                     $currentDateTime = new DateTime($currentTimeDatabase);
    //                     $checkInDateTime = new DateTime($checkInTimeDatabase);
    //                     $checkOutDateTime = new DateTime($checkOutTimeDatabase);
    //                     if ($inTime) {
    //                         $diff = date_diff($checkOutDateTime, $currentDateTime);
    //                     } else {
    //                         $diff = date_diff($currentDateTime, $checkInDateTime);
    //                     }
    //                     $diffNegative = $diff->format("%r");
    //                     if ($diffNegative == '-') {
    //                         return $this->redirect()->toRoute('registerAttendance', ['action' => 'checkin', 'userId' => $resultRow->USER_ID, 'type' => $attendanceType]);
    //                     }
    //                 }
    //                 // echo '<pre>'; print_r("1234"); die;
    //                 $result = $attendanceDetailRepo->getDtlWidEmpIdDate($employeeId, date(Helper::PHP_DATE_FORMAT));
    //                 if (!isset($result)) {
    //                     throw new Exception("Today's Attendance of employee with employeeId :$employeeId is not found.");
    //                 }
    //                 $attendanceModel = new Attendance();
    //                 $attendanceModel->employeeId = $employeeId;
    //                 $attendanceModel->attendanceDt = new Expression("TRUNC(SYSDATE)");
    //                 $attendanceModel->attendanceTime = new Expression("SYSDATE");
    //                 $attendanceModel->ipAddress = $request->getServer('REMOTE_ADDR');
    //                 $attendanceModel->attendanceFrom = 'WEB';
    //                 $attendanceModel->remarks = isset($postData['checkInRemarks']) ? $postData['checkInRemarks'] : '';
    //                 $attendanceRepo->add($attendanceModel);
    //                 // to add user log details in HRIS_USER_LOG
    //                 $this->setUserLog($this->adapter, $request->getServer('REMOTE_ADDR'), $resultRow->USER_ID);
    //                 $this->getAuthService()->clearIdentity();
    //                 $this->flashmessenger()->clearCurrentMessages();
    //                 $this->flashmessenger()->addMessage("Attendance Register Successfully!!!");
    //             }
    //         }
    //     }
    //     return $this->redirect()->toRoute($redirect);
    // }


    public function authenticateAction() {
        $request = $this->getRequest();
        $form = $this->getForm();
        $redirect = 'registerAttendance';
        if ($request->isPost()) {
            $postData = $request->getPost()->getArrayCopy();
            $form->setData($request->getPost());
            $storageData = $this->storage->read();
            $storage_userId = $storageData['user_id'];
            $sotrage_empID = $storageData['employee_id'];
            $sotrage_roleID = $storageData['employee_id'];
            if ($form->isValid()) {
                //check authentication...
                $this->getAuthService()->getAdapter()
                        ->setIdentity($request->getPost('username'))
                        ->setCredential($request->getPost('password'));
                $result = $this->getAuthService()->authenticate();
                foreach ($result->getMessages() as $message) {
                    $this->flashmessenger()->addMessage($message);
                }
                if ($result->isValid()) {
                    
                    if (isset($_COOKIE[$request->getPost('username')])) {
                        setcookie($request->getPost('username'), '', 1, "/");
                    }
                    //after authentication success get the user specific details
                    $resultRow = $this->getAuthService()->getAdapter()->getResultRowObject();

                    $allowRegisterAttendance = false;
                    $attendanceType = "IN";
                    if ($this->preference->allowSystemAttendance == 'Y') {
                        $employeeId = $resultRow->EMPLOYEE_ID;
                        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                        $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
                        $inTime = $todayAttendance['IN_TIME'];
                        $attendanceType = ($inTime) ? "OUT" : "IN";
                        $allowRegisterAttendance = ($todayAttendance['TRAVEL_ID'] == null && $todayAttendance['LEAVE_ID'] == null) ? true : false;
                    }

                    $employeeRepo = new EmployeeRepository($this->adapter);
                    $employeeDetail = $employeeRepo->employeeDetailSession($resultRow->EMPLOYEE_ID);

                    $companyRepo = new \Setup\Repository\CompanyRepository($this->adapter);
                    $companyDetail = $companyRepo->fetchById($employeeDetail['COMPANY_ID']);

                    $monthRepo = new MonthRepository($this->adapter);
                    $fiscalYear = $monthRepo->getCurrentFiscalYear();

                    $repository = new RolePermissionRepository($this->adapter);
                    $rawMenus = $repository->fetchAllMenuByRoleId($resultRow->ROLE_ID);
                    $menus = Helper::extractDbData($rawMenus);

                    $roleRepo = new RoleSetupRepository($this->adapter);
                    $acl = $roleRepo->fetchById($resultRow->ROLE_ID);
                    $acl['CONTROL'] = explode(',', $acl['CONTROL']);
                    $roleControlRepo = new RoleControlRepository($this->adapter);
                    $roleControlDetails = $roleControlRepo->fetchById($acl['ROLE_ID']);
                    $acl['CONTROL_VALUES']=$roleControlDetails;

                    $this->getAuthService()->getStorage()->write([
                        "user_name" => $request->getPost('username'),
                        "user_id" => $resultRow->USER_ID,
                        "employee_id" => $resultRow->EMPLOYEE_ID,
                        "role_id" => $resultRow->ROLE_ID,
                        "employee_detail" => $employeeDetail,
                        "fiscal_year" => $fiscalYear,
                        "menus" => $menus,
                        'register_attendance' => $attendanceType,
                        'allow_register_attendance' => $allowRegisterAttendance,
                        'acl' => (array) $acl,
                        'preference' => (array) $this->preference,
                        'company_detail' => $companyDetail
                    ]);


                    // to add user log details in HRIS_USER_LOG
                    $this->setUserLog($this->adapter, $request->getServer('REMOTE_ADDR'), $resultRow->USER_ID);

                    /*
                     * 
                     */
                    if (1 == $request->getPost('rememberme')) {
                        $this->getSessionStorage()
                                ->setRememberMe(1);
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    /*
                     * 
                     */

                    $redirect = 'login';
                    //after authentication success get the user specific details
                    $resultRow = $this->getAuthService()->getAdapter()->getResultRowObject();
                    $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                    $employeeId = $resultRow->EMPLOYEE_ID;
                    $attendanceRepo = new AttendanceRepository($this->adapter);
                    $preference = new Preference();
                    
                    if (isset($postData['checkInRemarks']) && ($preference->needApprovalForLateCheckIn == 'Y')) {
                        $this->attendanceRequest($postData, $employeeId);
                        $this->getAuthService()->clearIdentity();
                        return $this->redirect()->toRoute('login');
                    }
                    if (!isset($postData['checkInRemarks'])) {
                        $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
                        $inTime = $todayAttendance['IN_TIME'];
                        $halfDayFlag = $todayAttendance['HALFDAY_FLAG'];
                        $attendanceType = ($inTime) ? "OUT" : "IN";
                        $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
                        if (!$shiftDetails) {
                            $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift($employeeId);
                        }
                        $currentTimeDatabase = $shiftDetails['CURRENT_TIME'];
                        $checkInTimeDatabase = $shiftDetails['CHECKIN_TIME'];
                        $checkOutTimeDatabase = ($halfDayFlag == 'Y') ? $shiftDetails['HALF_DAY_CHECKOUT_TIME'] : $shiftDetails['CHECKOUT_TIME'];

                        $currentDateTime = new DateTime($currentTimeDatabase);
                        $checkInDateTime = new DateTime($checkInTimeDatabase);
                        $checkOutDateTime = new DateTime($checkOutTimeDatabase);
                        if ($inTime) {
                            $diff = date_diff($checkOutDateTime, $currentDateTime);
                        } else {
                            $diff = date_diff($currentDateTime, $checkInDateTime);
                        }
                        $diffNegative = $diff->format("%r");
                        if ($diffNegative == '-') {
                            return $this->redirect()->toRoute('registerAttendance', ['action' => 'checkin', 'userId' => 0, 'type' => $attendanceType]);
                        }
                    }
                    $result = $attendanceDetailRepo->getDtlWidEmpIdDate($employeeId, date(Helper::PHP_DATE_FORMAT));
                    if (!isset($result)) {
                        throw new Exception("Today's Attendance of employee with employeeId :$employeeId is not found.");
                    }
                    $attendanceModel = new Attendance();
                    $attendanceModel->employeeId = $employeeId;
                    $attendanceModel->attendanceDt = new Expression("TRUNC(SYSDATE)");
                    $attendanceModel->attendanceTime = new Expression("SYSDATE");
                    $attendanceModel->ipAddress = $request->getServer('REMOTE_ADDR');
                    $attendanceModel->attendanceFrom = 'WEB';
                    $attendanceModel->remarks = isset($postData['checkInRemarks']) ? $postData['checkInRemarks'] : '';
                    
                    $attendanceRepo->add($attendanceModel);
                    // to add user log details in HRIS_USER_LOG
                    $this->setUserLog($this->adapter, $request->getServer('REMOTE_ADDR'), $resultRow->USER_ID);
                    $this->getAuthService()->clearIdentity();
                    $this->flashmessenger()->clearCurrentMessages();
                    $this->flashmessenger()->addMessage("Attendance Register Successfully!!!");
                }
            }elseif($storage_userId != ""){
                $allowRegisterAttendance = false;
                $attendanceType = "IN";
                if ($this->preference->allowSystemAttendance == 'Y') {
                    $employeeId = $sotrage_empID;
                    $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                    $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
                    $inTime = $todayAttendance['IN_TIME'];
                    $attendanceType = ($inTime) ? "OUT" : "IN";
                    $allowRegisterAttendance = ($todayAttendance['TRAVEL_ID'] == null && $todayAttendance['LEAVE_ID'] == null) ? true : false;
                }

                $employeeRepo = new EmployeeRepository($this->adapter);
                $employeeDetail = $employeeRepo->employeeDetailSession($sotrage_empID);

                $companyRepo = new \Setup\Repository\CompanyRepository($this->adapter);
                $companyDetail = $companyRepo->fetchById($employeeDetail['COMPANY_ID']);

                $monthRepo = new MonthRepository($this->adapter);
                $fiscalYear = $monthRepo->getCurrentFiscalYear();

                $repository = new RolePermissionRepository($this->adapter);
                $rawMenus = $repository->fetchAllMenuByRoleId($sotrage_roleID);
                $menus = Helper::extractDbData($rawMenus);

                $roleRepo = new RoleSetupRepository($this->adapter);
                $acl = $roleRepo->fetchById($sotrage_roleID);
                $acl['CONTROL'] = explode(',', $acl['CONTROL']);
                $roleControlRepo = new RoleControlRepository($this->adapter);
                $roleControlDetails = $roleControlRepo->fetchById($acl['ROLE_ID']);
                $acl['CONTROL_VALUES']=$roleControlDetails;

                $this->getAuthService()->getStorage()->write([
                    "user_name" => $request->getPost('username'),
                    "user_id" => $storage_userId,
                    "employee_id" => $sotrage_empID,
                    "role_id" => $sotrage_roleID,
                    "employee_detail" => $employeeDetail,
                    "fiscal_year" => $fiscalYear,
                    "menus" => $menus,
                    'register_attendance' => $attendanceType,
                    'allow_register_attendance' => $allowRegisterAttendance,
                    'acl' => (array) $acl,
                    'preference' => (array) $this->preference,
                    'company_detail' => $companyDetail
                ]);


                // to add user log details in HRIS_USER_LOG
                $this->setUserLog($this->adapter, $request->getServer('REMOTE_ADDR'), $storage_userId);

                /*
                    * 
                    */
                if (1 == $request->getPost('rememberme')) {
                    $this->getSessionStorage()
                            ->setRememberMe(1);
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                }
                /*
                    * 
                    */

                $redirect = 'login';
                //after authentication success get the user specific details
                $resultRow = $this->getAuthService()->getAdapter()->getResultRowObject();
                $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                $employeeId = $sotrage_empID;
                $attendanceRepo = new AttendanceRepository($this->adapter);
                $preference = new Preference();
                
                if (isset($postData['checkInRemarks']) && ($preference->needApprovalForLateCheckIn == 'Y')) {
                    $this->attendanceRequest($postData, $employeeId);
                    $this->getAuthService()->clearIdentity();
                    return $this->redirect()->toRoute('login');
                }
                if (!isset($postData['checkInRemarks'])) {
                    $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
                    $inTime = $todayAttendance['IN_TIME'];
                    $halfDayFlag = $todayAttendance['HALFDAY_FLAG'];
                    $attendanceType = ($inTime) ? "OUT" : "IN";
                    $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
                    if (!$shiftDetails) {
                        $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift($employeeId);
                    }
                    $currentTimeDatabase = $shiftDetails['CURRENT_TIME'];
                    $checkInTimeDatabase = $shiftDetails['CHECKIN_TIME'];
                    $checkOutTimeDatabase = ($halfDayFlag == 'Y') ? $shiftDetails['HALF_DAY_CHECKOUT_TIME'] : $shiftDetails['CHECKOUT_TIME'];

                    $currentDateTime = new DateTime($currentTimeDatabase);
                    $checkInDateTime = new DateTime($checkInTimeDatabase);
                    $checkOutDateTime = new DateTime($checkOutTimeDatabase);
                    if ($inTime) {
                        $diff = date_diff($checkOutDateTime, $currentDateTime);
                    } else {
                        $diff = date_diff($currentDateTime, $checkInDateTime);
                    }
                    $diffNegative = $diff->format("%r");
                    if ($diffNegative == '-') {
                        return $this->redirect()->toRoute('registerAttendance', ['action' => 'checkin', 'userId' => 0,'type' => $attendanceType]);
                    }
                }
                $result = $attendanceDetailRepo->getDtlWidEmpIdDate($employeeId, date(Helper::PHP_DATE_FORMAT));
                if (!isset($result)) {
                    throw new Exception("Today's Attendance of employee with employeeId :$employeeId is not found.");
                }
                $attendanceModel = new Attendance();
                $attendanceModel->employeeId = $employeeId;
                $attendanceModel->attendanceDt = new Expression("TRUNC(SYSDATE)");
                $attendanceModel->attendanceTime = new Expression("SYSDATE");
                $attendanceModel->ipAddress = $request->getServer('REMOTE_ADDR');
                $attendanceModel->attendanceFrom = 'WEB';
                $attendanceModel->remarks = isset($postData['checkInRemarks']) ? $postData['checkInRemarks'] : '';
                $attendanceRepo->add($attendanceModel);
                // to add user log details in HRIS_USER_LOG
                $this->setUserLog($this->adapter, $request->getServer('REMOTE_ADDR'), $storage_userId);
                $this->getAuthService()->clearIdentity();
                $this->flashmessenger()->clearCurrentMessages();
                $this->flashmessenger()->addMessage("Attendance Register Successfully!!!");
                
            }
        }
        return $this->redirect()->toRoute($redirect);
    }

    // public function checkinAction()
    // {
    //     $userId = $this->params()->fromRoute('userId');
    //     $storageData = $this->storage->read();
    //     $userId = $storageData['user_id'];
    //     $type = $this->params()->fromRoute('type');
    //     $userRepository = new UserSetupRepository($this->adapter);
    //     $userDetail = $userRepository->fetchById($userId);
    //     $employeeId = $userDetail['EMPLOYEE_ID'];

    //     if ($userDetail['USER_NAME'] != $this->userName['user_name']) {
    //         $this->getSessionStorage()->forgetMe();
    //         $this->getAuthService()->clearIdentity();
    //         $this->flashmessenger()->addMessage("Access Denied!! Please use valid user id");
    //         return $this->redirect()->toRoute("registerAttendance");
    //     }


    //     $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
    //     $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');

    //     $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
    //     if (!$shiftDetails) {
    //         $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift($employeeId);
    //     }
    //     if ($todayAttendance[AttendanceDetail::HALFDAY_FLAG] == 'Y') {
    //         $shiftDetails['CHECKOUT_TIME'] = $shiftDetails['HALF_DAY_CHECKOUT_TIME'];
    //     }

    //     return Helper::addFlashMessagesToArray($this, [
    //         'username' => $userDetail['USER_NAME'],
    //         'password' => $userDetail['PASSWORD'],
    //         'type' => $type,
    //         'attendanceDetails' => $todayAttendance,
    //         'shiftDetails' => $shiftDetails,
    //         'userDetail' => $userDetail
    //     ]);
    // }

    public function checkinAction() {
        $storageData = $this->storage->read();
        $userId = $storageData['user_id'];
        //$type = $this->params()->fromRoute('type');
        $rawType = $this->params()->fromRoute('type', '');
        // Allow ONLY: letters, numbers, underscore, dash, dot
        $safeType = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', (string) $rawType);
        // Enforce max length (prevents long payload injections)
        $safeType = substr($safeType, 0, 10);
        // If type becomes empty after sanitization, assign a safe default
        if ($safeType === '') {
            $safeType = 'IN';
        }
        $userRepository = new UserSetupRepository($this->adapter);
        $userDetail = $userRepository->fetchById($userId);
        $employeeId = $userDetail['EMPLOYEE_ID'];

        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
        $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');

        $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
        if (!$shiftDetails) {
            $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift($employeeId);
        }
        if ($todayAttendance[AttendanceDetail::HALFDAY_FLAG] == 'Y') {
            $shiftDetails['CHECKOUT_TIME'] = $shiftDetails['HALF_DAY_CHECKOUT_TIME'];
        }

        return Helper::addFlashMessagesToArray($this, [
                    'username' => $userDetail['USER_NAME'],
                    //'type' => $type,
                    'type' => $safeType,
                    'attendanceDetails' => $todayAttendance,
                    'shiftDetails' => $shiftDetails
        ]);

    }
   
    private function setUserLog(AdapterInterface $adapter, $clientIp, $userId)
    {
        $userLogRepo = new UserLogRepository($adapter);

        $userLog = new UserLog();
        $userLog->loginIp = $clientIp;
        $userLog->userId = $userId;

        $userLogRepo->add($userLog);
    }

    public function checkoutAction()
    {
        $employeeId = $this->storage->read()['employee_id'];

        $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
        $shiftDetails = $attendanceDetailRepo->fetchEmployeeShfitDetails($employeeId);
        if (!$shiftDetails) {
            $shiftDetails = $attendanceDetailRepo->fetchEmployeeDefaultShift();
        }
        $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
        $inTime = $todayAttendance['IN_TIME'];


        $currentTimeDatabase = $shiftDetails['CURRENT_TIME'];
        $checkInTimeDatabase = $shiftDetails['CHECKIN_TIME'];
        $checkOutTimeDatabase = $shiftDetails['CHECKOUT_TIME'];

        $currentDateTime = new DateTime($currentTimeDatabase);
        $checkInDateTime = new DateTime($checkInTimeDatabase);
        $checkOutDateTime = new DateTime($checkOutTimeDatabase);

        $attendanceType = 'IN';
        if ($inTime) {
            $attendanceType = 'OUT';
            $diff = date_diff($checkOutDateTime, $currentDateTime);
        } else {
            $diff = date_diff($currentDateTime, $checkInDateTime);
        }
        $diffNegative = $diff->format("%r");

        $request = $this->getRequest();
        $remarks = '';

        if ($diffNegative == '-') {
            if (!$request->isPost()) {
                return Helper::addFlashMessagesToArray($this, [
                    'type' => $attendanceType,
                    'attendanceDetails' => $todayAttendance,
                    'shiftDetails' => $shiftDetails
                ]);
            } else {
                $postData = $request->getPost();
                $remarks = $postData['remarks'];
            }
        }

        $attendanceRepo = new AttendanceRepository($this->adapter);
        $attendanceModel = new Attendance();

        $attendanceModel->employeeId = $this->getAuthService()->getStorage()->read()['employee_id'];
        $attendanceModel->attendanceDt = new Expression("TRUNC(SYSDATE)");
        $attendanceModel->attendanceTime = new Expression("SYSDATE");
        $attendanceModel->ipAddress = $request->getServer('REMOTE_ADDR');
        $attendanceModel->attendanceFrom = 'WEB';
        $attendanceModel->remarks = $remarks;
        
        $attendanceRepo->add($attendanceModel);

        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
        $msg = 'Attendance Registered Successfully!!!';
        $this->flashmessenger()->addMessage($msg);
        return $this->redirect()->toRoute('login');
    }

    public function attendanceRequest($postData, $employeeId)
    {
        $attendanceModel = new AttendanceRequestModel();
        $attendanceModel->employeeId = $employeeId;
        $attendanceModel->attendanceDt = new Expression('TRUNC(SYSDATE)');
        $attendanceModel->id = ((int) Helper::getMaxId($this->adapter, $attendanceModel::TABLE_NAME, "ID")) + 1;
        //
        $currTime = $postData['time'];
        if ($postData['type'] == 'IN') {
            $attendanceModel->inTime = new Expression("TO_DATE('" . $currTime . "', 'HH:MI AM')");
            $attendanceModel->outTime = NULL;
            $attendanceModel->inRemarks = $postData['checkInRemarks'];
        } else {
            $attendanceModel->inTime = NULL;
            $attendanceModel->outTime = new Expression("TO_DATE('" . $currTime . "', 'HH:MI AM')");
            $attendanceModel->outRemarks = $postData['checkInRemarks'];
        }
        $attendanceModel->status = "RQ";


        $attendanceRepo = new AttendanceRequestRepository($this->adapter);
        $attendanceRepo->add($attendanceModel);

        try {
            HeadNotification::pushNotification(NotificationEvents::ATTENDANCE_APPLIED, $attendanceModel, $this->adapter, $this);
        } catch (Exception $e) {
            $this->flashmessenger()->addMessage($e->getMessage());
        }
        $this->flashmessenger()->addMessage("Attendance Request Submitted Successfully!!");
    }

    public function loginAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
        return $this->redirect()->toRoute('login');
    }
}
