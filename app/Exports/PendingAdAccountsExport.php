<?php

namespace App\Exports;

use App\Models\AdAccount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class processingAdAccountsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return AdAccount::query()
            ->where('status', 'pending')
            ->with(['user', 'organization'])
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Organization',
            'Status',
            'Created By',
            'Created At',
        ];
    }

    public function map($adAccount): array
    {
        return [
            $adAccount->id,
            $adAccount->name,
            $adAccount->type,
            $adAccount->organization->name,
            ucfirst($adAccount->status),
            $adAccount->user->name,
            $adAccount->created_at->format('Y-m-d H:i:s'),
        ];
    }
}