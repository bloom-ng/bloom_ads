<?php

namespace App\Exports;

use App\Models\AdAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcessingAdAccountsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return AdAccount::where('status', 'processing')
            ->with(['user', 'organization'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Organization',
            'Created By',
            'Created At',
            'Status'
        ];
    }

    public function map($adAccount): array
    {
        return [
            $adAccount->id,
            $adAccount->name,
            $adAccount->type,
            $adAccount->organization->name,
            $adAccount->user->name,
            $adAccount->created_at->format('Y-m-d'),
            ucfirst($adAccount->status)
        ];
    }
} 