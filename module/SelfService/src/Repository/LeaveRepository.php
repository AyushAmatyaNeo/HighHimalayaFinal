<?php

namespace SelfService\Repository;

use Application\Repository\HrisRepository;
use LeaveManagement\Model\LeaveAssign;
use Traversable;
use Zend\Db\Adapter\AdapterInterface;

class LeaveRepository extends HrisRepository
{

  public function __construct(AdapterInterface $adapter, $tableName = null)
  {
    if ($tableName == null) {
      $tableName = LeaveAssign::TABLE_NAME;
    }
    parent::__construct($adapter, $tableName);
  }

  function selectAll($employeeId): Traversable
  {
    $sql = "SELECT A.*,LEAVE_TAKEN_INT||' hr' as LEAVE_TAKEN,
    LEAVE_APP_PENDING_INT||' hr' as LEAVE_APP_PENDING,
     (intbalancehr-LEAVE_APP_PENDING_INT) || ' hr' as AVAILABLE_BALANCE FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.PREVIOUS_YEAR_BAL_HR || ' hr' as PREVIOUS_YEAR_BAL_HR,
                  la.total_hours || ' hr' as total_hours,
                  la.balance_hour || ' hr' as balance_hour,
                  la.balance_hour as intbalancehr,
                  (SELECT nvl(SUM(elr.total_hours/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END)),0)
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                   LEFT JOIN (SELECT * FROM HRIS_LEAVE_YEARS  WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE ) LY ON (1=1)
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS IN ('AP','CP','CR')
                  -- AND ELR.START_DATE BETWEEN LY.START_DATE AND LY.END_DATE
                  ) AS LEAVE_TAKEN_INT,
                  (SELECT NVL(SUM(elr.total_hours/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END)),0)
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                   LEFT JOIN (SELECT * FROM HRIS_LEAVE_YEARS  WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE ) LY ON (1=1)
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS NOT IN ('AP','CP','CR', 'C', 'R')
                  -- AND ELR.START_DATE BETWEEN LY.START_DATE AND LY.END_DATE
                  ) AS LEAVE_APP_PENDING_INT,
                  (SELECT NVL(SUM(HS.ENCASH_DAYS),0)
                  FROM HRIS_EMP_SELF_LEAVE_CLOSING HS
                  WHERE HS.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND HS.LEAVE_ID     =LA.LEAVE_ID
                  ) AS ENCASHED,
                  (SELECT NVL(SUM(EPD.NO_OF_DAYS),0)
                  FROM HRIS_EMPLOYEE_PENALTY_DAYS EPD
                  WHERE EPD.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND EPD.LEAVE_ID     =LA.LEAVE_ID
                  ) AS LEAVE_DEDUCTED,
                  (SELECT NVL(SUM(ELA.NO_OF_DAYS),0)
                  FROM HRIS_EMPLOYEE_LEAVE_ADDITION ELA
                  WHERE ELA.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELA.LEAVE_ID     =LA.LEAVE_ID
                  ) AS LEAVE_ADDED
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID     =LMS.LEAVE_ID)
                WHERE LA.EMPLOYEE_ID=:employeeId AND LMS.STATUS ='E' AND LMS.IS_MONTHLY = 'N' ORDER BY LMS.LEAVE_ENAME ASC) A";
    // dd($sql); die;
    $boundedParameter = [];
    $boundedParameter['employeeId'] = $employeeId;
    // echo '<pre>';print_r($sql);die;
    $statement = $this->adapter->query($sql);
    return $statement->execute($boundedParameter);
  }

  function monthlyLeaveStatus($employeeId, $fiscalYearMonthNo)
  {
    $sql = "SELECT * FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.TOTAL_HOURS AS TOTAL_DAYS,
                  LA.PREVIOUS_YEAR_BAL_HR AS PREVIOUS_YEAR_BAL,
                  LA.BALANCE_HOUR as BALANCE,
                  (SELECT SUM(ELR.TOTAL_HOURS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  AND ELR.START_DATE BETWEEN MTH.FROM_DATE AND MTH.TO_DATE
                  ) AS LEAVE_TAKEN
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID =LMS.LEAVE_ID)
                LEFT JOIN (SELECT * FROM HRIS_LEAVE_MONTH_CODE WHERE 
                LEAVE_YEAR_ID=(SELECT LEAVE_YEAR_ID FROM HRIS_LEAVE_YEARS 
                WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE)) MTH
                ON (MTH.LEAVE_YEAR_MONTH_NO= LA.FISCAL_YEAR_MONTH_NO)
                WHERE LA.EMPLOYEE_ID        =:employeeId
                AND LA.FISCAL_YEAR_MONTH_NO =:fiscalYearMonthNo
                AND LMS.STATUS              ='E'
                AND LMS.IS_MONTHLY          = 'Y'
                AND LMS.CARRY_FORWARD          = 'N'
                ORDER BY LMS.LEAVE_ENAME ASC)
                UNION ALL
                SELECT * FROM (SELECT LA.LEAVE_ID,
                  LMS.LEAVE_CODE,
                  LMS.LEAVE_ENAME,
                  LA.TOTAL_HOURS AS TOTAL_DAYS,
                  LA.PREVIOUS_YEAR_BAL_HR AS PREVIOUS_YEAR_BAL,
                  LA.BALANCE_HOUR AS BALANCE,
                  (SELECT SUM(ELR.TOTAL_HOURS/(
                    CASE
                      WHEN ELR.HALF_DAY IN ('F','S')
                      THEN 2
                      ELSE 1
                    END))
                  FROM HRIS_EMPLOYEE_LEAVE_REQUEST ELR
                  WHERE ELR.LEAVE_ID =LA.LEAVE_ID
                  AND ELR.EMPLOYEE_ID=LA.EMPLOYEE_ID
                  AND ELR.STATUS     ='AP'
                  AND ELR.START_DATE <= MTH.TO_DATE
                  ) AS LEAVE_TAKEN
                FROM HRIS_EMPLOYEE_LEAVE_ASSIGN LA
                LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS
                ON (LA.LEAVE_ID =LMS.LEAVE_ID)
                LEFT JOIN (SELECT * FROM HRIS_LEAVE_MONTH_CODE WHERE 
                LEAVE_YEAR_ID=(SELECT LEAVE_YEAR_ID FROM HRIS_LEAVE_YEARS 
                WHERE TRUNC(SYSDATE) BETWEEN START_DATE AND END_DATE)) MTH
                ON (MTH.LEAVE_YEAR_MONTH_NO= LA.FISCAL_YEAR_MONTH_NO)
                WHERE LA.EMPLOYEE_ID        =:employeeId
                AND LA.FISCAL_YEAR_MONTH_NO =:fiscalYearMonthNo
                AND LMS.STATUS              ='E'
                AND LMS.IS_MONTHLY          = 'Y'
                AND LMS.CARRY_FORWARD          = 'Y'
                ORDER BY LMS.LEAVE_ENAME ASC) ";

    $boundedParameter = [];
    $boundedParameter['employeeId'] = $employeeId;
    $boundedParameter['fiscalYearMonthNo'] = $fiscalYearMonthNo;
// dd($sql, $employeeId, $fiscalYearMonthNo);
    $statement = $this->adapter->query($sql);
    return $statement->execute($boundedParameter);
  }
}
