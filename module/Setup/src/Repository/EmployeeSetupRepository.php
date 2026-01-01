<?php

namespace Setup\Repository;
use AttendanceManagement\Repository\RoasterRepo;
use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\EmployeeSetup;

class EmployeeSetupRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
       // $this->logTable = new TableGateway(Logs::TABLE_NAME, $adapter);
        $this->tableGateway = new TableGateway(EmployeeSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $array = $model->getArrayCopyForDB();
    
        //echo '<pre>';print_r($array);die;
    
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $array = $model->getArrayCopyForDB(); 

    }

    public function edit(Model $model, $id)
    { 
        $array = $model->getArrayCopyForDB();
        //echo '<pre>';print_r($array);die;
        unset($array[EmployeeSetup::ID]);
        
        $this->tableGateway->update($array, [EmployeeSetup::ID => $id]);
        
    }
    // public function edit(Model $model, $id) {
    //     $temp = $model->getArrayCopyForDB();
    //     //echo '<pre>';print_r($temp);die;
    //     if (!isset($temp[EmployeeSetup::ID])) {
    //         $temp[EmployeeSetup::ID] = null;
    //     }
    //     $this->tableGateway->update($temp, [EmployeeSetup::ID => $id]);
    // }

    public function delete($id)
    {
        // $this->tableGateway->update([EmployeeSetup::STATUS => 'D'], [EmployeeSetup::ID => $id]);
        $this->tableGateway->update([EmployeeSetup::STATUS => 'D'], [EmployeeSetup::ID => $id]);
        //echo'<pre>';print_r($id);die;
    }
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchGroupDetails()
    {
        $sql = "SELECT * FROM DOMMY_EMPLOYEES where status='E'";
        $statement=$this->adapter->query($sql);
        $result=$statement->execute();
       // echo '<pre>';print_r($sql);die;
        return $result;
    }


    public function fetchById($id)
    {
        // echo'<pre>';print_r("ff");die;
        $row = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(EmployeeSetup::class, [EmployeeSetup::ID]), false);
            $select->where([EmployeeSetup::ID => $id]);
        });
        return $row->current();
    }
}
