<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\LetterSetup;
use Setup\Model\LetterSetupVariable;
use Setup\Model\SubLetterSetup;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LetterSetupRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;
    private $variableGateway;


    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LetterSetup::TABLE_NAME, $adapter);
        // $this->SubtableGateway = new TableGateway(SubLetterSetup::TABLE_NAME, $adapter);
        // $this->variableGateway = new TableGateway(LetterSetupVariable::TABLE_NAME, $adapter);

        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [LetterSetup::LETTER_SETUP_ID => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LetterSetup::class, [LetterSetup::LETTER_TITLE]), false);
            $select->where([LetterSetup::STATUS => EntityHelper::STATUS_ENABLED]);
            $select->where([LetterSetup::PARENT_ID => null]); // Corrected closing bracket
            $select->order([LetterSetup::LETTER_TITLE => Select::ORDER_ASCENDING]);
        });
    }

    public function fetchSubLetterAll($parentId)
    {
        return $this->tableGateway->select(function (Select $select) use ($parentId) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LetterSetup::class, [LetterSetup::LETTER_TITLE]), false);

            $select->where([
                LetterSetup::STATUS => EntityHelper::STATUS_ENABLED,
                LetterSetup::PARENT_ID => $parentId
            ]);

            $select->order([LetterSetup::LETTER_SETUP_ID => Select::ORDER_DESCENDING]);
        });
    }

    // public function fetchAllVariables()
    // {
    //     return $this->variableGateway->select(function (Select $select) {
    //         $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LetterSetupVariable::class, [LetterSetupVariable::VARIABLE_NAME]), false);
    //         $select->where([
    //             LetterSetupVariable::STATUS => EntityHelper::STATUS_ENABLED,
    //         ]);
    //         $select->order([LetterSetupVariable::LETTER_SETUP_VARIABLE_ID => Select::ORDER_ASCENDING]);
    //     });
    // }


    public function fetchById($id)
    {
        $sql = "SELECT 
                ls.LETTER_SETUP_ID, 
                ls.LETTER_TITLE AS LETTER_TITLE,
                ls.TOP_POSITION AS TOP_POSITION,
                ls.BOTTOM_POSITION AS BOTTOM_POSITION,
                ls.LEFT_POSITION AS LEFT_POSITION,
                ls.RIGHT_POSITION AS RIGHT_POSITION,
                ls.IS_CUSTOM AS IS_CUSTOM,
                lsd.description AS description 
            FROM 
                hris_letter_setup ls
            LEFT JOIN 
                hris_letter_setup_detail lsd
            ON 
                lsd.LETTER_SETUP_ID = ls.LETTER_SETUP_ID
            WHERE 
                ls.LETTER_SETUP_ID = :id
            ORDER BY lsd.letter_setup_detail_id ASC";

        $statement = $this->tableGateway->getAdapter()->createStatement($sql, [':id' => $id]);
        $result = $statement->execute();

        $collection = [];
        foreach ($result as $row) {
            $letterSetupId = $row['LETTER_SETUP_ID'];

            if (!isset($collection[$letterSetupId])) {
                $collection[$letterSetupId] = [
                    'LETTER_SETUP_ID' => $row['LETTER_SETUP_ID'],
                    'LETTER_TITLE' => $row['LETTER_TITLE'],
                    'TOP_POSITION' => $row['TOP_POSITION'],
                    'BOTTOM_POSITION' => $row['BOTTOM_POSITION'],
                    'LEFT_POSITION' => $row['LEFT_POSITION'],
                    'RIGHT_POSITION' => $row['RIGHT_POSITION'],
                    'IS_CUSTOM' => $row['IS_CUSTOM'],
                    'DESCRIPTIONS' => [],
                ];
            }

            $collection[$letterSetupId]['DESCRIPTIONS'][] = $row['DESCRIPTION']->load();
        }

        if (empty($collection)) {
            throw new \Exception("Could not find row $id");
        }
        return array_values($collection)[0];
    }


    public function hintWord()
    {
        $sql = "SELECT 
                DBMS_LOB.SUBSTR(lsd.description, 4000, 1) AS description 
            FROM 
                hris_letter_setup_detail lsd";

        $statement = $this->tableGateway->getAdapter()->createStatement($sql);
        $result = $statement->execute();

        $uniqueWords = [];

        foreach ($result as $row) {
            $cleanText = strip_tags(html_entity_decode($row['DESCRIPTION']));
            $words = preg_split('/\s+/', $cleanText);

            foreach ($words as $word) {
                $uniqueWords[$word] = true;
            }
        }

        return array_keys($uniqueWords);
    }


    public function assignLetter($mergedLetterSetupIds, $employeeIds)
    {
        $sqlCheck = "SELECT COUNT(*) as count FROM HRIS_EMPLOYEE_LETTER_ASSIGN 
                     WHERE LETTER_SETUP_ID = :letterSetupId AND EMPLOYEE_ID = :employeeId";

        $sqlInsert = "INSERT INTO HRIS_EMPLOYEE_LETTER_ASSIGN (
                        LETTER_SETUP_ID,
                        EMPLOYEE_ID
                    ) VALUES (
                        :letterSetupId,
                        :employeeId
                    )";

        $checkStatement = $this->adapter->query($sqlCheck);
        $insertStatement = $this->adapter->query($sqlInsert);

        foreach ($mergedLetterSetupIds as $letterSetupId) {
            foreach ($employeeIds as $employeeId) {
                $checkParams = [
                    'letterSetupId' => $letterSetupId,
                    'employeeId' => $employeeId
                ];

                $checkResult = $checkStatement->execute($checkParams)->current();

                if ($checkResult['COUNT'] == 0) {
                    try {
                        $insertStatement->execute($checkParams);
                    } catch (\PDOException $e) {
                        if ($e->getCode() == 1) {
                            continue;
                        } else {
                            throw new \Exception($e->getMessage());
                        }
                    }
                }
            }
        }
    }




    public function deleteDetails($id)
    {
        $sql = "DELETE FROM hris_letter_setup_detail WHERE letter_setup_id = :id";
        $statement = $this->adapter->createStatement($sql);
        $parameters = ['id' => $id];

        try {
            $result = $statement->execute($parameters);
            return $result->getAffectedRows();
        } catch (\Exception $e) {

            throw new \Exception("Failed to delete details: " . $e->getMessage());
        }
    }



    public function delete($id)
    {
        $this->tableGateway->update([LetterSetup::STATUS => 'D'], [LetterSetup::LETTER_SETUP_ID => $id]);
    }

    public function fetchChildLits($parentId)
    {
        $boundedParams = [];
        $sql = <<<EOT
              SELECT LETTER_TITLE ,LETTER_SETUP_ID FROM HRIS_LETTER_SETUP WHERE PARENT_ID=:parentId AND STATUS='E'    
EOT;
        $boundedParams['parentId'] = $parentId;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute($boundedParams);
        return Helper::extractDbData($result);
    }

    public function fetchEmployeeList($empIds, $letterId)
    {
        $empIds = array_map('intval', $empIds);
        $empIdsList = implode(',', $empIds);

        $sql = "SELECT 
            E.FULL_NAME,
            E.EMPLOYEE_ID,
            LS.LETTER_TITLE,
            L.LETTER_SETUP_ID,
            D.DEPARTMENT_NAME,
            B.BRANCH_NAME,
            DE.DESIGNATION_TITLE,
            C.COMPANY_NAME
        FROM HRIS_EMPLOYEE_LETTER_ASSIGN L
        LEFT JOIN HRIS_LETTER_SETUP LS
            ON LS.LETTER_SETUP_ID = L.LETTER_SETUP_ID
        LEFT JOIN HRIS_EMPLOYEES E
            ON E.EMPLOYEE_ID = L.EMPLOYEE_ID
        LEFT JOIN 
            HRIS_DEPARTMENTS D
        ON 
            E.DEPARTMENT_ID = D.DEPARTMENT_ID
        LEFT JOIN 
            HRIS_BRANCHES B
        ON
        E.BRANCH_ID=B.BRANCH_ID
        LEFT JOIN 
            HRIS_DESIGNATIONS DE
        ON 
            DE.DESIGNATION_ID=E.DESIGNATION_ID
        LEFT JOIN 
            HRIS_COMPANY C
        ON 
            E.COMPANY_ID=C.COMPANY_ID
        WHERE 
            E.EMPLOYEE_ID IN ($empIdsList) 
        AND L.LETTER_SETUP_ID = $letterId";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return Helper::extractDbData($result);
    }


    public function fetchEmployeeLetterList($empId)
    {

        $sql = "SELECT 
                    HLS.LETTER_TITLE  
                FROM 
                    HRIS_EMPLOYEE_LETTER_ASSIGN HLA
                LEFT JOIN 
                    HRIS_LETTER_SETUP HLS
                ON 
                    HLA.LETTER_SETUP_ID = HLS.LETTER_SETUP_ID
                WHERE 
                    HLA.EMPLOYEE_ID = $empId";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }
}
