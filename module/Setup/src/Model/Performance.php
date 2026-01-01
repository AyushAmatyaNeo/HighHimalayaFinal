<?php

namespace Setup\Model;

use Application\Model\Model;

class Performance extends Model {
    public $indexId;
    public $percentRange;
    public $percentDesc;
    public $status;
    public $createdBy;
    public $createdDate;
    public $modifiedBy;
    public $modifiedDate;
   
   
    const TABLE_NAME = "HRIS_PERFORMANCE_INDEX";
    const INDEX_ID = "INDEX_ID";
    const PERCENT_RANGE = "PERCENT_RANGE";
    const PERCENT_DESC = "PERCENT_DESC";
    const CREATED_BY="CREATED_BY";
    const CREATED_DATE="CREATED_DATE";
    const MODIFIED_BY="MODIFIED_BY";
    const MODIFIED_DATE="MODIFIED_DATE";
    const STATUS ="STATUS";

    
    public $mappings = [
        'indexId' => self::INDEX_ID,
        'percentRange' => self::PERCENT_RANGE,
        'percentDesc'=>self::PERCENT_DESC,
        'createdBy'=>self::CREATED_BY,
        'createdDate'=>self::CREATED_DATE,
        'modifiedBy'=>self::MODIFIED_BY,
        'modifiedDate'=>self::MODIFIED_DATE,
        'status'=>self::STATUS
    ];

}
