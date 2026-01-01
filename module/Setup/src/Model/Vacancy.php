<?php

namespace Setup\Model;

use Application\Model\Model;

class Vacancy extends Model {

    const TABLE_NAME="HRIS_VACANCY";
    const VACANCY_ID="VACANCY_ID";
    const POSITION="POSITION";
    const AVAILABILITY="AVAILABILITY";
    const DESCRIPTION="DESCRIPTION";
    const CREATED_BY="CREATED_BY";
    const CREATED_DT="CREATED_DT";
    const MODIFIED_DT='MODIFIED_DT';
    const MODIFIED_BY='MODIFIED_BY';
    const STATUS="STATUS";
    const VACANCY_STATUS="VACANCY_STATUS";

    public $vacancyId;
    public $position;
    public $avaliability;
    public $description;
    public $createdBy;
    public $createdDt;
    public $modifiedDt;
    public $modifiedBy;
    public $status;
    public $vacancyStatus;

    public $mappings=[
        'vacancyId'=>self::VACANCY_ID,
        'position'=>self::POSITION,
        'avaliability'=>self::AVAILABILITY,
        'description'=>self::DESCRIPTION,
        'createdBy'=>self::CREATED_BY,
        'createdDt'=>self::CREATED_DT,
        'modifiedDt' =>self::MODIFIED_DT,
        'modifiedBy' =>self::MODIFIED_BY,
        'status'=>self::STATUS,
        'vacancyStatus'=>self::VACANCY_STATUS
    ];

}
