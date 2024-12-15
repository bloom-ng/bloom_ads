<?php

namespace App\Exports;

use App\Models\AdAccount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class FilteredAdAccountsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Timezone',
            'Currency',
            'Provider',
            'Provider ID',
            'Status',
            'Business Manager ID',
            'Landing Page',
            'Organization',
            'Created By',
            'Created At',
            'Updated At'
        ];
    }

    public function map($adAccount): array
    {
        return [
            $adAccount->id,
            $adAccount->name,
            $adAccount->type,
            $adAccount->timezone,
            $adAccount->currency,
            $adAccount->provider,
            $adAccount->provider_id,
            ucfirst($adAccount->status),
            $adAccount->business_manager_id,
            $adAccount->landing_page,
            $adAccount->organization->name,
            $adAccount->user->name,
            $adAccount->created_at->format('Y-m-d H:i:s'),
            $adAccount->updated_at->format('Y-m-d H:i:s')
        ];
    }
} 