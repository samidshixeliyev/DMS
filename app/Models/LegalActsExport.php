<?php

namespace App\Exports;

use App\Models\LegalAct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegalActsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query->get();
        }
        
        return LegalAct::with(['issuingAuthority', 'executor', 'category', 'status'])
            ->active()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Document Number',
            'Document Date',
            'Title',
            'Issuing Authority',
            'Executor',
            'Category',
            'Status',
            'Execution Deadline',
            'Notes',
            'Created At',
        ];
    }

    public function map($legalAct): array
    {
        return [
            $legalAct->id,
            $legalAct->document_number,
            $legalAct->document_date?->format('Y-m-d'),
            $legalAct->title,
            $legalAct->issuingAuthority?->name,
            $legalAct->executor?->name,
            $legalAct->category?->name,
            $legalAct->status?->name,
            $legalAct->execution_deadline?->format('Y-m-d'),
            $legalAct->notes,
            $legalAct->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
