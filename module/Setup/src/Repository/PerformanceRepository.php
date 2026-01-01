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
use Setup\Model\Performance;

class PerformanceRepository implements RepositoryInterface
{

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
       // $this->logTable = new TableGateway(Logs::TABLE_NAME, $adapter);
        $this->tableGateway = new TableGateway(Performance::TABLE_NAME, $adapter);
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
        unset($array[Performance::INDEX_ID]);
        
        $this->tableGateway->update($array, [Performance::INDEX_ID => $id]);
        
    }

    public function delete($id)
    {
        $this->tableGateway->update([Performance::STATUS => 'D'], [Performance::INDEX_ID => $id]);
        //echo'<pre>';print_r($id);die;
    }
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function fetchGroupDetails()
    {
        // echo("hi");
        // $sql = "SELECT * FROM DOMMY_EMPLOYEES where status='E'";
        $sql = "SELECT * FROM HRIS_PERFORMANCE_INDEX where status='E'";
        $statement=$this->adapter->query($sql);
        $result=$statement->execute();
        //echo '<pre>';print_r($result);die;
        //echo '<pre>';print_r($sql);die;
        return $result;
    }


    public function fetchById($id)
    {
        // echo'<pre>';print_r("ff");die;
        $row = $this->tableGateway->select(function (Select $select) use ($id) {
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(Performance::class, [Performance::INDEX_ID]), false);
            $select->where([Performance::INDEX_ID => $id]);
        });
        return $row->current();
    }
}
