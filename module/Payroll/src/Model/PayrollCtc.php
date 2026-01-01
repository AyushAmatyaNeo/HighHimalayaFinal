<?php

namespace Payroll\Model;

use Application\Model\Model;

class PayrollCtc extends Model {

    CONST TABLE_NAME = "HRIS_PAYROLL_CTC";
    CONST ID = "ID";
    CONST FLAT_ID = "FLAT_ID";
    CONST NAME = "NAME";
    CONST TYPE = "TYPE";
    CONST VALUE = "VALUE";
    CONST IS_DELETED = "IS_DELETED";
    CONST ORDER_NUMBER = "ORDER_NUMBER";

    public $id;
    public $flatId;
    public $name;
    public $type;
    public $value;
    public $isDeleted;
    public $orderNumber;

    public $mappings = [
        'id' => self::ID,
        'flatId' => self::FLAT_ID,
        'name' => self::NAME,
        'type' => self::TYPE,
        'value' => self::VALUE,
        'isDeleted' => self::IS_DELETED,
        'orderNumber' => self::ORDER_NUMBER
    ];

}
