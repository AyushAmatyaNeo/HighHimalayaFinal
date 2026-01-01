<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;


class VariableRepository  implements RepositoryInterface {

    private $adapter;

    public function __construct(AdapterInterface $adapter, $tableName = null) {
        $this->adapter = $adapter;
      
    }
    public function fetchAll() {
        return '';
    
    }

    public function fetchById($id) {
      
        return '';
    }

    public function add(Model $model) {
       
    }

    public function delete($model) {
        return '';
    
    }

    public function edit(Model $model, $id) {
        return '';
      
    }

    public function getVariables($empId = null)
    {
        return [
            'Employee Name' => $empId ? $this->fetchEmployeeName($empId) : '',
            // 'Permanent Address' =>$empId ? $this->fetchEmployeePermanentAddress($empId) : '', 
            'Nepali Name'=>$empId ? $this->fetchEmployeeNepaliName($empId) : '',
            'Employee email' => $empId ? $this->fetchEmployeeEmail($empId) : '',
            'To Honorific'=> $empId ? $this->fetchEmployeeHonorific($empId) : '',
        ];
    }

    protected function rawQuery($sql,$paramenter = []): array {
        $statement = $this->adapter->query($sql);
        $iterator = $statement->execute($paramenter);
        return iterator_to_array($iterator, false);
    }
    
    private function fetchEmployeeDetail($employeeId, $column) {
        if (empty($employeeId) || empty($column)) {
            return ''; 
        }
    
        $boundedParameter = ['employeeId' => $employeeId];
        $sql = "
                SELECT $column
                FROM HRIS_EMPLOYEES
                WHERE EMPLOYEE_ID = :employeeId
                ";
        
        $resultList = $this->rawQuery($sql, $boundedParameter);
    
        if (empty($resultList) || !isset($resultList[0][$column])) {
            return ''; 
        }
        
        $detail = $resultList[0][$column];
        
        return $detail !== null ? $detail : '';
    }

    

    public function fetchEmployeeNepaliName($employeeId) {
        return $this->fetchEmployeeDetail($employeeId, 'NAME_NEPALI');
    }
    
    public function fetchEmployeeName($employeeId) {
        return $this->fetchEmployeeDetail($employeeId, 'FULL_NAME');
    }

    // public function fetchEmployeePermanentAddress($employeeId){
    //     return $this->fetchEmployeeDetail($employeeId, 'ADDR_PERM_WARD_NO');
    // }
    
    public function fetchEmployeeEmail($employeeId) {
        return $this->fetchEmployeeDetail($employeeId, 'EMAIL_OFFICIAL');
    }
    
    public function fetchEmployeeHonorific($employeeId) {
        $gender_type = $this->fetchEmployeeDetail($employeeId, 'GENDER_ID');
        $marital_status = $this->fetchEmployeeDetail($employeeId, 'MARITAL_STATUS');
        
        if ($gender_type == 1) {
            return 'Mr';
        } elseif ($gender_type == 2) {
            if ($marital_status == 'M') {
                return 'Mrs';
            } elseif ($marital_status == 'U') {
                return 'Miss';
            } else {
                return 'Mx'; 
            }
        } else {
            return 'Mx';
        }
    }

}
