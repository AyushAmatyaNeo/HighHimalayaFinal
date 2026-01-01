<?php

namespace Setup\Model;

use Application\Model\Model;

class PerformanceSetup extends Model {
    public $performanceId;
    public $categoryName;
    public $credit;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
   
    const TABLE_NAME = "HRIS_PERFORMANCE_SETUP";
    const PERFORMANCE_ID = "PERFORMANCE_ID";
    const CATEGORY_NAME = "CATEGORY_NAME";
    const CREDIT = "CREDIT";
    const CREATED_BY="CREATED_BY";
    const CREATED_DATE="CREATED_DATE";
    const MODIFIED_BY="MODIFIED_BY";
    const MODIFIED_DATE="MODIFIED_DATE";
    const STATUS ="STATUS";

    
    public $mappings = [
        'performanceId' => self::PERFORMANCE_ID,
        'categoryName' => self::CATEGORY_NAME,
        'credit'=>self::CREDIT,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'status'=>self::STATUS
    ];

}
