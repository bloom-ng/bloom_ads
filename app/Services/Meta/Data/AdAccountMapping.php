<?php

namespace App\Services\Meta\Data;

class AdAccountMapping {

    public static function accountStatusMapping()
    {
        return [
            1 => 'ACTIVE',
            2 => 'DISABLED',
            3 => 'UNSETTLED',
            7 => 'PENDING_RISK_REVIEW',
            8 => 'PENDING_SETTLEMENT',
            9 => 'IN_GRACE_PERIOD',
            100 => 'PENDING_CLOSURE',
            101 => 'CLOSED',
            201 => 'ANY_ACTIVE',
            202 => 'ANY_CLOSED',
        ];
    } 
}

