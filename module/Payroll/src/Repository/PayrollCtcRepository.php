<?php

namespace Payroll\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\PayrollCtc;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class PayrollCtcRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(PayrollCtc::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $array = $model->getArrayCopyForDB();
        // echo "<pre>"; print_r($array); die;
        $this->tableGateway->insert($array);
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        // echo "<pre>"; print_r($array); die;
        unset($array[PayrollCtc::ID]);

        $this->tableGateway->update($array, [PayrollCtc::ID => $id]);
    }

    public function delete($id) 
    {
        $this->tableGateway->update([PayrollCtc::IS_DELETED => 'Y'], [PayrollCtc::ID => $id]);
    }

    public function fetchAll() {}

    public function fetchById($id)
    {
        $sql = "SELECT * FROM HRIS_PAYROLL_CTC WHERE ID = $id";

        // Execute the query using the adapter
        $statement = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        // Fetch the results
        $results = $statement->current(); // Convert the result to an array

        // Return the results
        return $results;
    }

    public function fetchActiveRecord()
    {
        // Define the SQL query to fetch data from the HRIS_NEW_APPRAISAL_PERIOD table
        $sql = "SELECT 
            PC.ID,
            PC.NAME,
            PC.TYPE,
            CASE 
                WHEN PC.VALUE IS NOT NULL THEN PC.VALUE || '%' 
                ELSE '---------' 
            END AS VALUE
        FROM HRIS_PAYROLL_CTC PC
        LEFT JOIN HRIS_FLAT_VALUE_SETUP FVS ON FVS.FLAT_ID = PC.FLAT_ID
        WHERE PC.IS_DELETED = 'N' ORDER BY PC.ORDER_NUMBER ASC";


        // Execute the query using the adapter
        $statement = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        // Fetch the results
        $results = $statement->toArray(); // Convert the result to an array

        // Return the results
        return $results;
    }

    public function fetchPayrollDetails($search)
    {
        $employeeId = $search['employeeId'];
        $fiscalyearId = $search['fiscalYear'];

        $sql = "SELECT 
                    E.FULL_NAME,
                    PC.ID,
                    PC.NAME,
                    FVS.FLAT_EDESC, 
                    CASE 
                        WHEN PC.FLAT_ID IS NOT NULL THEN 
                            (SELECT FLAT_VALUE FROM HRIS_FLAT_VALUE_DETAIL 
                                WHERE FLAT_ID = PC.FLAT_ID 
                                AND EMPLOYEE_ID = $employeeId 
                                AND FISCAL_YEAR_ID = $fiscalyearId
                            )
                        ELSE 
                            NULL
                    END AS FLAT_VALUE,
                    PC.TYPE, 
                    PC.VALUE,
                    E.MARITAL_STATUS,
                    E.SSF_PF
                FROM 
                    HRIS_PAYROLL_CTC PC
                LEFT JOIN 
                    HRIS_FLAT_VALUE_SETUP FVS ON FVS.FLAT_ID = PC.FLAT_ID
                 LEFT JOIN 
                     HRIS_EMPLOYEES E ON E.EMPLOYEE_ID = $employeeId
                WHERE 
                    PC.IS_DELETED = 'N'
                ORDER BY 
                    PC.ORDER_NUMBER ASC
                ";

        // echo "<pre>"; print_r($sql); die;
        // Execute the query using the adapter
        $statement = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        // Fetch the results
        $results = $statement->toArray(); // Convert the result to an array

        return $results;
    }

    public function fetchFlatSetup()
    {
        // Define the SQL query to fetch data from the HRIS_NEW_APPRAISAL_PERIOD table
        $sql = "SELECT * FROM HRIS_FLAT_VALUE_SETUP WHERE STATUS = 'E' ORDER BY FLAT_ID ASC";

        // Execute the query using the adapter
        $statement = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        // Fetch the results
        $results = $statement->toArray(); // Convert the result to an array

        return $results;
    }

    public function fetchAllEmployee()
    {
        $sql = "SELECT EMPLOYEE_ID, EMPLOYEE_CODE||'-'||FULL_NAME AS FULL_NAME
                FROM HRIS_EMPLOYEES
                  WHERE STATUS = 'E'  AND (SERVICE_TYPE_ID IN (SELECT SERVICE_TYPE_ID FROM HRIS_SERVICE_TYPES WHERE TYPE NOT IN ('RESIGNED','RETIRED')) OR SERVICE_TYPE_ID IS NULL)
                AND RETIRED_FLAG = 'N' AND RESIGNED_FLAG = 'N'";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $list = [];
        // $list[-1] = 'All Employee';
        foreach ($result as $data) {
            $list[$data['EMPLOYEE_ID']] = $data['FULL_NAME'];
        }
        return $list;
    }
}
