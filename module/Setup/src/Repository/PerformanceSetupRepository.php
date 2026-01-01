<?php

namespace Setup\Repository;
use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Setup\Model\PerformanceSetup;

class PerformanceSetupRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
       // $this->logTable = new TableGateway(Logs::TABLE_NAME, $adapter);
        $this->tableGateway = new TableGateway(PerformanceSetup::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        $array = $model->getArrayCopyForDB();
    
        //echo '<pre>';print_r($array);die;
    
        $this->tableGateway->insert($model->getArrayCopyForDB());
        // $array = $model->getArrayCopyForDB(); 
        //echo '<pre>';print_r($model->getArrayCopyForDB());die;
    }

    public function edit(Model $model, $id)
    { 
        $array = $model->getArrayCopyForDB();
        //echo '<pre>';print_r($array);die;
        unset($array[PerformanceSetup::PERFORMANCE_ID]);
        
        $this->tableGateway->update($array, [PerformanceSetup::PERFORMANCE_ID => $id]);
        
    }

    public function delete($id)
    {
        $this->tableGateway->update([PerformanceSetup::STATUS => 'D'], [PerformanceSetup::PERFORMANCE_ID => $id]);
        //echo'<pre>';print_r($id);die;
    }
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchGroupDetails()
    {
        // $sql = "SELECT * FROM HRIS_PERFORMANCE_SETUP where status='E'";
        $sql = "SELECT * FROM HRIS_PERFORMANCE_SETUP where status='E'";
        $statement=$this->adapter->query($sql);
        $result=$statement->execute();
        //echo '<pre>';print_r($result);die;
        return $result;
    }


    public function fetchById($id)
    {
        // echo'<pre>';print_r("ff");die;
        $row = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(PerformanceSetup::class, [PerformanceSetup::PERFORMANCE_ID]), false);
            $select->where([PerformanceSetup::PERFORMANCE_ID => $id]);
        });
        return $row->current();
    }
}
