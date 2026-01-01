<?php

namespace Setup\Repository;

use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\LetterSetupDetail;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class LetterSetupDetailRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LetterSetupDetail::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [LetterSetupDetail::LETTER_SETUP_ID => $id]);
    }

    public function fetchAll()
    {
        // return $this->tableGateway->select(function (Select $select) {
        //     $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(LetterSetup::class, [LetterSetup::LETTER_TITLE]), false);
        //     $select->where([LetterSetup::STATUS => EntityHelper::STATUS_ENABLED]);
        //     $select->order([LetterSetup::LETTER_TITLE => Select::ORDER_ASCENDING]);
        // });
    }

    public function fetchById($id)
    {
        $sql = "SELECT LETTER_SETUP_ID, 
                        LETTER_TITLE,
                        TOP_POSITION,
                        BOTTOM_POSITION,
                        LEFT_POSITION,
                        RIGHT_POSITION,
                        IS_CUSTOM,
                       DBMS_LOB.SUBSTR(description, 4000, 1) AS description 
                FROM hris_letter_setup 
                WHERE LETTER_SETUP_ID = :id";
    
        $statement = $this->tableGateway->getAdapter()->createStatement($sql, [':id' => $id]);
        $result = $statement->execute();
        $row = $result->current();
    
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
    
        return $row;
    }
    

    public function delete($id)
    {
        // $this->tableGateway->update([LetterSetup::STATUS => 'D'], [LetterSetup::LETTER_SETUP_ID => $id]);
    }
}
