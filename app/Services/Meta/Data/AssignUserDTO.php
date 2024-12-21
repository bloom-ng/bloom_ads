<?php

namespace App\Services\Meta\Data;

class AssignUserDTO extends MetaDataTransferObject
{
    public string $adAccountId;
    public string $userId;
    public array $tasks;
} 