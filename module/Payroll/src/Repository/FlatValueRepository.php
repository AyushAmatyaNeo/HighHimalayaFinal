<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 10/3/16
 * Time: 1:36 PM
 */

namespace Payroll\Repository;


use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Payroll\Model\FlatValue;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Application\Helper\EntityHelper;
use Zend\Db\Sql\Select;
use Setup\Model\Logs;

class FlatValueRepository implements RepositoryInterface
{
    private $adapter;
    private $tableGateway;
    private $logTable;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter=$adapter;
        $this->logTable = new TableGateway(Logs::TABLE_NAME, $adapter);
        $this->tableGateway = new TableGateway(FlatValue::TABLE_NAME, $adapter);
    }

    public function add(Model $model)
    {
        // dd("hi");
        // $array = $model->getArrayCopyForDB();
        // $branch = new BranchRepository($this->adapter);
        // $logs = new Logs();
        // $logs->module = 'FlatValue';
        // $logs->operation = 'I';
        // $logs->createdBy = $array['CREATED_BY'];
        // $logs->createdDesc = 'flat id - ' . $array['FLAT_ID'];
        // $logs->tableDesc = 'HRIS_FLAT_VALUE_SETUP';
        // $branch->insertLogs($logs);

        // return $this->tableGateway->insertLogs($model->getArrayCopyForDB());
        $array = $model->getArrayCopyForDB();

        $flatCode = substr($array['FLAT_CODE'], 0, 3);
        $array['FLAT_CODE'] = $flatCode;
        $this->tableGateway->insert($array);
    }

    public function edit(Model $model, $id)
    {
        // $array = $model->getArrayCopyForDB();
        // // echo '<pre>';print_r($array);die;
        // unset($array[FlatValue::FLAT_ID]);
        // unset($array[FlatValue::CREATED_DT]);
        // $this->tableGateway->update($array, [FlatValue::FLAT_ID => $id]);
        // $branch = new BranchRepository($this->adapter);
        // $logs = new Logs();
        // $logs->module = 'FlatValue';
        // $logs->operation = 'U';
        // $logs->modifiedBy = $array['CREATED_BY'];
        // $logs->modifiedDesc = 'Flat id - ' . $id;
        // $logs->tableDesc = 'HRIS_FLAT_VALUE_SETUP';

        // $branch->updateLogs($logs);
        return $this->tableGateway->update($model->getArrayCopyForDB(),[FlatValue::FLAT_ID=>$id]);
    }
    public function delete($id)
    {
        // $this->tableGateway->update([FlatValue::STATUS => 'D'], [FlatValue::FLAT_ID => $id]);
        // $branch = new BranchRepository($this->adapter); 
        // $logs = new Logs();
        // $logs->module = 'FlatValue';
        // $logs->operation = 'D';
        // $logs->deletedBy = $id;
        // $logs->deletedDesc = 'FlatValue id - ' . $id;
        // $logs->tableDesc = 'HRIS_FLAT_VALUE_SETUP';
        // $branch->deleteLogs($logs);
        return $this->tableGateway->update([FlatValue::STATUS => 'D'], [FlatValue::FLAT_ID => $id]);
    }

    public function fetchAll()
    {
        return $this->tableGateway->select(function(Select $select){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(FlatValue::class,
                    [FlatValue::FLAT_EDESC, FlatValue::FLAT_LDESC]),false);
            $select->where([FlatValue::STATUS=>'E']);
        });
    }

    public function fetchById($id)
    {
        return $this->tableGateway->select(function(Select $select) use($id){
            $select->columns(EntityHelper::getColumnNameArrayWithOracleFns(FlatValue::class,
                    [FlatValue::FLAT_EDESC, FlatValue::FLAT_LDESC]),false);
            $select->where([FlatValue::FLAT_ID=>$id]);    
        })->current();
    }

    // public function delete($id)
    // {
    //     return $this->tableGateway->update([FlatValue::STATUS=>'D'],[FlatValue::FLAT_ID=>$id]);
    // }
}