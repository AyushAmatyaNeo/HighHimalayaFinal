<?php

namespace Setup\Model;

use Application\Model\Model;

class EmployeeSetup extends Model {
    public $id;
    public $employeeId;
    public $employeeCode;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
   
    const TABLE_NAME = "DOMMY_EMPLOYEES";
    const ID = "ID";
    const EMPLOYEE_ID = "EMPLOYEE_ID";
    const EMPLOYEE_CODE = "EMPLOYEE_CODE";
    
    const CREATED_BY="CREATED_BY";
    const CREATED_DATE="CREATED_DATE";
    const MODIFIED_BY="MODIFIED_BY";
    const MODIFIED_DATE="MODIFIED_DATE";
    const STATUS ="STATUS";
    

    public $mappings = [
        'id' => self::ID,
        'employeeId' => self::EMPLOYEE_ID,
        'employeeCode' => self::EMPLOYEE_CODE,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'status'=>self::STATUS
        
    ];

}
