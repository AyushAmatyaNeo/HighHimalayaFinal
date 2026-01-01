<?php

namespace Setup\Repository;

use Application\Helper\EntityHelper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Setup\Model\Vacancy;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class VacancyRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(Vacancy::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

   

    public function fetchAll()
{
    return $this->tableGateway->select(function (Select $select) {
        // Selecting specific columns
        $columns = EntityHelper::getColumnNameArrayWithOracleFns(
            Vacancy::class,
            [Vacancy::POSITION, 'VACANCY_ID', 'AVAILABILITY', 'DESCRIPTION']
        );
        $select->columns($columns, false);

        // Adding a WHERE clause
        $select->where([Vacancy::STATUS => EntityHelper::STATUS_ENABLED]);

        // Adding an ORDER BY clause
        $select->order([Vacancy::VACANCY_ID => Select::ORDER_ASCENDING]);
    });
}


    public function fetchById($id) {
        $rowset = $this->tableGateway->select(function(Select $select) use($id) {
            $select->where([
                Vacancy::VACANCY_ID => $id,
                Vacancy::STATUS => EntityHelper::STATUS_ENABLED
            ]);
        });
        return $rowset->current();
    }


    public function edit(Model $model, $id) {
        $array = $model->getArrayCopyForDB();
        $this->tableGateway->update($array, [Vacancy::VACANCY_ID => $id]);
    }

   
    

    public function delete($id) {
        $this->tableGateway->update([
            Vacancy::STATUS => EntityHelper::STATUS_DISABLED], [
            Vacancy::VACANCY_ID => $id
        ]);
    }

}
