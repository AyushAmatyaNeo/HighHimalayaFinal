<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;


class VariableRepository  implements RepositoryInterface
{

    private $adapter;

    public function __construct(AdapterInterface $adapter, $tableName = null)
    {
        $this->adapter = $adapter;
    }
    public function fetchAll()
    {
        return '';
    }

    public function fetchById($id)
    {

        return '';
    }

    public function add(Model $model) {}

    public function delete($model)
    {
        return '';
    }

    public function edit(Model $model, $id)
    {
        return '';
    }

    public function getVariables($empId = null)
    {
        return [
            'Employee Name' => $empId ? $this->fetchEmployeeName($empId) : '',
            'Employee Code' => $empId ? $this->fetchEmployeeCode($empId) : '',
            'Temporary Address' => $empId ? $this->fetchEmployeeTemporaryAddress($empId) : '',
            'Permanent Address' => $empId ? $this->fetchEmployeePermanentAddress($empId) : '',
            'Nepali Name' => $empId ? $this->fetchEmployeeNepaliName($empId) : '',
            'Employee Email' => $empId ? $this->fetchEmployeeEmail($empId) : '',
            'To Honorific' => $empId ? $this->fetchEmployeeHonorific($empId) : '',
            'PAN No' => $empId ? $this->fetchEmployeePanNo($empId) : '',
            'Date' => $empId ? $this->fetchCurrentDate() : '',
            'Mobile No' => $empId ? $this->fetchEmployeeMobileNo($empId) : '',
            'Father Name' => $empId ? $this->fetchEmployeeFatherName($empId) : '',
            'Mother Name' => $empId ? $this->fetchEmployeeMotherName($empId) : '',
            'Grand Father Name' => $empId ? $this->fetchEmployeeGrandFatherName($empId) : '',
            'Emerg Contact Name' => $empId ? $this->fetchEmployeeEmergContactName($empId) : '',
            'Emerg Contact Address' => $empId ? $this->fetchEmployeeEmergContactAddress($empId) : '',
            'Emerg Contact No' => $empId ? $this->fetchEmployeeEmergContactNo($empId) : '',
            'Citizenship No' => $empId ? $this->fetchEmployeeCitizenshipNo($empId) : '',
            'Citizenship Issue Place' => $empId ? $this->fetchEmployeeCitizenshipIssuePlace($empId) : '',
            'Citizenship Issue Date' => $empId ? $this->fetchEmployeeCitizenshipIssueDate($empId) : '',
            'Bank Name' => $empId ? $this->fetchEmployeeBankName($empId) : '',
            'Account No' => $empId ? $this->fetchEmployeeAccountNo($empId) : '',
            'License No' => $empId ? $this->fetchEmployeeLicenseNo($empId) : '',
            'License Expiry Date' => $empId ? $this->fetchEmployeeLicenseExpiryDate($empId) : '',
            'Telephone No' => $empId ? $this->fetchEmployeeTelephoneNo($empId) : '',
            'Joined Date' => $empId ? $this->fetchEmployeeJoinedDate($empId) : '',
            'Position' => $empId ? $this->fetchEmployeePosition($empId) : '',
            'Branch' => $empId ? $this->fetchEmployeeBranch($empId) : '',
            'Gross Salary' => $empId ? $this->fetchEmployeeGrossSalary($empId) : '',
            'Basic Salary' => $empId ? $this->fetchEmployeeSalary($empId) : '',
            'Allowance' => $empId ? $this->fetchEmployeeAllowance($empId) : '',
            'Gratuity' => $empId ? $this->fetchEmployeeGratuity($empId) : '',
            'PF' => $empId ? $this->fetchEmployeePf($empId) : '',
            'FROM BRANCH' => $empId ? $this->fetchEmployeeFromBranch($empId) : '',
            'TO BRANCH' => $empId ? $this->fetchEmployeeToBranch($empId) : '',
            'EFFECTIVE FROM' => $empId ? $this->fetchEmployeeEffectiveFrom($empId) : '',
            'TO BASIC SALARY' => $empId ? $this->fetchEmployeeToSalary($empId) : '',
            'TO GROSS SALARY' => $empId ? $this->fetchEmployeeToGrossSalary($empId) : '',
        ];
    }

    protected function rawQuery($sql, $paramenter = []): array
    {
        $statement = $this->adapter->query($sql);
        $iterator = $statement->execute($paramenter);
        return iterator_to_array($iterator, false);
    }

    private function fetchEmployeeDetail($employeeId, $column)
    {
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



    public function fetchEmployeeNepaliName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'NAME_NEPALI');
    }

    public function fetchEmployeeName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'FULL_NAME');
    }

    private function fetchEmployeeTemporaryAddress($employeeId)
    {
        if (empty($employeeId)) {
            return '';
        }

        $boundedParameter = [
            'employeeId' => $employeeId
        ];

        $sql = "
        SELECT
            TRIM(
                CASE WHEN V.VDC_MUNICIPALITY_NAME IS NOT NULL
                    THEN V.VDC_MUNICIPALITY_NAME || '- '
                ELSE '' END ||

                CASE WHEN E.ADDR_TEMP_WARD_NO IS NOT NULL
                    THEN E.ADDR_TEMP_WARD_NO || ', '
                ELSE '' END ||

                CASE WHEN D.DISTRICT_NAME IS NOT NULL
                    THEN D.DISTRICT_NAME || ', '
                ELSE '' END ||

                CASE WHEN P.PROVINCE_NAME IS NOT NULL
                    THEN P.PROVINCE_NAME || ', '
                ELSE '' END ||

                CASE WHEN Z.ZONE_NAME IS NOT NULL
                    THEN Z.ZONE_NAME || ', '
                ELSE '' END ||

                CASE WHEN C.COUNTRY_NAME IS NOT NULL
                    THEN C.COUNTRY_NAME
                ELSE '' END
            ) AS TEMPORARY_ADDRESS
        FROM HRIS_EMPLOYEES E
        LEFT JOIN HRIS_VDC_MUNICIPALITIES V
            ON E.ADDR_TEMP_VDC_MUNICIPALITY_ID = V.VDC_MUNICIPALITY_ID
        LEFT JOIN HRIS_DISTRICTS D
            ON E.ADDR_TEMP_DISTRICT_ID = D.DISTRICT_ID
        LEFT JOIN HRIS_PROVINCES P
            ON E.ADDR_TEMP_PROVINCE_ID = P.PROVINCE_ID
        LEFT JOIN HRIS_ZONES Z
            ON E.ADDR_TEMP_ZONE_ID = Z.ZONE_ID
        LEFT JOIN HRIS_COUNTRIES C
            ON E.ADDR_TEMP_COUNTRY_ID = C.COUNTRY_ID
        WHERE E.EMPLOYEE_ID = :employeeId
    ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        return $resultList[0]['TEMPORARY_ADDRESS'] ?? '';
    }

    private function fetchEmployeePermanentAddress($employeeId)
    {
        if (empty($employeeId)) {
            return '';
        }

        $boundedParameter = [
            'employeeId' => $employeeId
        ];

        $sql = "
        SELECT
            TRIM(
                CASE WHEN V.VDC_MUNICIPALITY_NAME IS NOT NULL
                    THEN V.VDC_MUNICIPALITY_NAME || '- '
                ELSE '' END ||

                CASE WHEN E.ADDR_PERM_WARD_NO IS NOT NULL
                    THEN E.ADDR_PERM_WARD_NO || ', '
                ELSE '' END ||

                CASE WHEN D.DISTRICT_NAME IS NOT NULL
                    THEN D.DISTRICT_NAME || ', '
                ELSE '' END ||

                CASE WHEN P.PROVINCE_NAME IS NOT NULL
                    THEN P.PROVINCE_NAME || ', '
                ELSE '' END ||

                CASE WHEN Z.ZONE_NAME IS NOT NULL
                    THEN Z.ZONE_NAME || ', '
                ELSE '' END ||

                CASE WHEN C.COUNTRY_NAME IS NOT NULL
                    THEN C.COUNTRY_NAME
                ELSE '' END
            ) AS PERMANENT_ADDRESS
        FROM HRIS_EMPLOYEES E
        LEFT JOIN HRIS_VDC_MUNICIPALITIES V
            ON E.ADDR_PERM_VDC_MUNICIPALITY_ID = V.VDC_MUNICIPALITY_ID
        LEFT JOIN HRIS_DISTRICTS D
            ON E.ADDR_PERM_DISTRICT_ID = D.DISTRICT_ID
        LEFT JOIN HRIS_PROVINCES P
            ON E.ADDR_PERM_PROVINCE_ID = P.PROVINCE_ID
        LEFT JOIN HRIS_ZONES Z
            ON E.ADDR_PERM_ZONE_ID = Z.ZONE_ID
        LEFT JOIN HRIS_COUNTRIES C
            ON E.ADDR_PERM_COUNTRY_ID = C.COUNTRY_ID
        WHERE E.EMPLOYEE_ID = :employeeId
    ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        return $resultList[0]['PERMANENT_ADDRESS'] ?? '';
    }

    public function fetchEmployeeEmail($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'EMAIL_OFFICIAL');
    }

    public function fetchEmployeeCode($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'EMPLOYEE_CODE');
    }

    public function fetchEmployeePanNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'ID_PAN_NO');
    }

    public function fetchEmployeeHonorific($employeeId)
    {
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

    public function fetchCurrentDate()
    {
        return date('M j, Y');
    }

    public function fetchEmployeeMobileNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'MOBILE_NO');
    }

    public function fetchEmployeeFatherName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'FAM_FATHER_NAME');
    }

    public function fetchEmployeeMotherName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'FAM_MOTHER_NAME');
    }

    public function fetchEmployeeGrandFatherName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'FAM_GRAND_FATHER_NAME');
    }

    public function fetchEmployeeEmergContactName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'EMRG_CONTACT_NAME');
    }

    public function fetchEmployeeEmergContactAddress($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'EMERG_CONTACT_ADDRESS');
    }

    public function fetchEmployeeEmergContactNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'EMERG_CONTACT_NO');
    }

    public function fetchEmployeeCitizenshipNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'ID_CITIZENSHIP_NO');
    }

    private function fetchEmployeeCitizenshipIssuePlace($employeeId)
    {
        if (empty($employeeId)) {
            return '';
        }

        $boundedParameter = [
            'employeeId' => $employeeId
        ];

        $sql = "
            SELECT D.DISTRICT_NAME AS BRANCH
            FROM HRIS_EMPLOYEES E
            LEFT JOIN HRIS_DISTRICTS D
                ON E.ID_CITIZENSHIP_ISSUE_PLACE = D.DISTRICT_ID
            WHERE E.EMPLOYEE_ID = :employeeId
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if (empty($resultList) || !isset($resultList[0]['BRANCH'])) {
            return '';
        }

        return $resultList[0]['BRANCH'] ?? '';
    }

    public function fetchEmployeeCitizenshipIssueDate($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'ID_CITIZENSHIP_ISSUE_DATE');
    }

    public function fetchEmployeeBankName($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'BANK_ID');
    }

    public function fetchEmployeeAccountNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'ID_ACCOUNT_NO');
    }

    public function fetchEmployeeLicenseNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'ID_DRIVING_LICENCE_NO');
    }

    public function fetchEmployeeLicenseExpiryDate($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'ID_DRIVING_LICENCE_EXPIRY');
    }

    public function fetchEmployeeTelephoneNo($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'TELEPHONE_NO');
    }

    public function fetchEmployeeJoinedDate($employeeId)
    {
        return $this->fetchEmployeeDetail($employeeId, 'JOIN_DATE');
    }

    private function fetchEmployeePosition($employeeId)
    {
        if (empty($employeeId)) {
            return '';
        }

        $boundedParameter = [
            'employeeId' => $employeeId
        ];

        $sql = "
            SELECT P.POSITION_NAME AS POSITION
            FROM HRIS_EMPLOYEES E
            LEFT JOIN HRIS_POSITIONS P
                ON E.POSITION_ID = P.POSITION_ID
            WHERE E.EMPLOYEE_ID = :employeeId
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if (empty($resultList) || !isset($resultList[0]['POSITION'])) {
            return '';
        }

        return $resultList[0]['POSITION'] ?? '';
    }

    private function fetchEmployeeBranch($employeeId)
    {
        if (empty($employeeId)) {
            return '';
        }

        $boundedParameter = [
            'employeeId' => $employeeId
        ];

        $sql = "
            SELECT B.BRANCH_NAME AS BRANCH
            FROM HRIS_EMPLOYEES E
            LEFT JOIN HRIS_BRANCHES B
                ON E.BRANCH_ID = B.BRANCH_ID
            WHERE E.EMPLOYEE_ID = :employeeId
        ";

        $resultList = $this->rawQuery($sql, $boundedParameter);

        if (empty($resultList) || !isset($resultList[0]['BRANCH'])) {
            return '';
        }

        return $resultList[0]['BRANCH'] ?? '';
    }

    public function fetchEmployeeGrossSalary($employeeId)
    {
        $value = $this->fetchEmployeeDetail($employeeId, 'GROSS_SALARY');

        if ($value === '' || $value === null) {
            return '';
        }

        return $this->formatNepaliNumber($value);
    }

    public function fetchEmployeeSalary($employeeId)
    {
        $value = $this->fetchEmployeeDetail($employeeId, 'SALARY');

        if ($value === '' || $value === null) {
            return '';
        }

        return $this->formatNepaliNumber($value);
    }

    public function fetchEmployeeAllowance($employeeId)
    {
        $value = $this->fetchEmployeeDetail($employeeId, 'ALLOWANCE');

        if ($value === '' || $value === null) {
            return '';
        }

        return $this->formatNepaliNumber($value);
    }

    public function fetchEmployeeGratuity($employeeId)
    {
        $value = $this->fetchEmployeeDetail($employeeId, 'GRATUITY');

        if ($value === '' || $value === null) {
            return '';
        }

        return $this->formatNepaliNumber($value);
    }

    public function fetchEmployeePf($employeeId)
    {
        $value = $this->fetchEmployeeDetail($employeeId, 'SALARY_PF');

        if ($value === '' || $value === null) {
            return '';
        }

        return $this->formatNepaliNumber($value);
    }

    public function fetchEmployeeFromBranch($employeeId)
    {
        $boundedParameter = ['employeeId' => $employeeId];

        $sql = "
        SELECT *
        FROM (
            SELECT B.BRANCH_NAME
            FROM HRIS_JOB_HISTORY J
            LEFT JOIN HRIS_BRANCHES B ON B.BRANCH_ID = J.FROM_BRANCH_ID
            WHERE J.EMPLOYEE_ID = :employeeId
            ORDER BY J.START_DATE DESC
        )
        WHERE ROWNUM = 1
    ";

        $result = $this->rawQuery($sql, $boundedParameter);

        return $result[0]['BRANCH_NAME'] ?? null;
    }

    public function fetchEmployeeToBranch($employeeId)
    {
        $boundedParameter = ['employeeId' => $employeeId];

        $sql = "
        SELECT *
        FROM (
            SELECT B.BRANCH_NAME
            FROM HRIS_JOB_HISTORY J
            LEFT JOIN HRIS_BRANCHES B ON B.BRANCH_ID = J.TO_BRANCH_ID
            WHERE J.EMPLOYEE_ID = :employeeId
            ORDER BY J.START_DATE DESC
        )
        WHERE ROWNUM = 1
    ";

        $result = $this->rawQuery($sql, $boundedParameter);

        return $result[0]['BRANCH_NAME'] ?? null;
    }

    public function fetchEmployeeEffectiveFrom($employeeId)
    {
        $boundedParameter = ['employeeId' => $employeeId];

        $sql = "
        SELECT *
        FROM (
            SELECT START_DATE
            FROM HRIS_JOB_HISTORY
            WHERE EMPLOYEE_ID = :employeeId
            ORDER BY START_DATE DESC
        )
        WHERE ROWNUM = 1
    ";

        $result = $this->rawQuery($sql, $boundedParameter);

        return $result[0]['START_DATE'] ?? null;
    }

    public function fetchEmployeeToSalary($employeeId)
    {
        $boundedParameter = ['employeeId' => $employeeId];

        $sql = "
        SELECT *
        FROM (
            SELECT TO_SALARY
            FROM HRIS_JOB_HISTORY
            WHERE EMPLOYEE_ID = :employeeId
            ORDER BY START_DATE DESC
        )
        WHERE ROWNUM = 1
    ";

        $result = $this->rawQuery($sql, $boundedParameter);

        $value = $result[0]['TO_SALARY'] ?? null;
        return $this->formatNepaliNumber($value);
    }

    public function fetchEmployeeToGrossSalary($employeeId)
    {
        $boundedParameter = ['employeeId' => $employeeId];

        $sql = "
        SELECT *
        FROM (
            SELECT TO_GROSS_SALARY
            FROM HRIS_JOB_HISTORY
            WHERE EMPLOYEE_ID = :employeeId
            ORDER BY START_DATE DESC
        )
        WHERE ROWNUM = 1
    ";

        $result = $this->rawQuery($sql, $boundedParameter);

        $value = $result[0]['TO_GROSS_SALARY'] ?? null;
        return $this->formatNepaliNumber($value);
    }

    private function formatNepaliNumber($number)
    {
        if ($number === null || $number === '') {
            return '';
        }

        $number = (string) $number;

        // Handle decimal part if exists
        if (strpos($number, '.') !== false) {
            [$integer, $decimal] = explode('.', $number, 2);
        } else {
            $integer = $number;
            $decimal = '';
        }

        $lastThree = substr($integer, -3);
        $rest = substr($integer, 0, -3);

        if ($rest !== '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $formatted = $rest . ',' . $lastThree;
        } else {
            $formatted = $integer;
        }

        return $decimal !== ''
            ? $formatted . '.' . $decimal
            : $formatted;
    }
}
