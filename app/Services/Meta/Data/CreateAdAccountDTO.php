<?php

namespace App\Services\Meta\Data;

class CreateAdAccountDTO extends MetaDataTransferObject
{
    public string $name;
    public string $currency;
    public int $timezoneId;
    public string $endAdvertiser;
    public string $mediaAgency;
    public string $partner;
    public bool $invoice;
} 