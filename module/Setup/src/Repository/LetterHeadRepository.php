<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\LetterHead;
use Setup\Model\LetterSetup;
use Setup\Model\LetterSetupVariable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LetterHeadRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;


    public function __construct(AdapterInterface $adapter)
    {
        $this->tableGateway = new TableGateway(LetterHead::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function add(Model $model)
    {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function edit(Model $model, $id)
    {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [LetterHead::FILE_CODE => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function (Select $select) {

        });
    }

    public function fetchById($id)
    {
        $rowset = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->where([LetterHead::FILE_CODE => $id]);
            $select->order('CREATED_DT DESC')->limit(1);
        });
        return $rowset->current();
    }

    public function delete($id)
    {
       return [];        
    }
    public function createLetterHead()
    {
      $sql = "INSERT INTO HRIS_LETTER_HEAD (FILE_CODE, EMPLOYEE_ID, FILE_PATH, CREATED_DT, MODIFIED_DT, FILE_NAME)
        VALUES (1 ,NULL, 'null', SYSDATE, NULL, 'null')";
      $statement = $this->adapter->query($sql);
      $result = $statement->execute();
      return Helper::extractDbData($result);
    }

}
