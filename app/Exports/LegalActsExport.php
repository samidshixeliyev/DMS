<?php

namespace App\Exports;

class LegalActsExport
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function download(string $filename)
    {
        $legalActs = $this->query ? $this->query->get() : collect();
        $html = $this->generateExcelHtml($legalActs);

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

    protected function generateExcelHtml($legalActs): string
    {
        $date = now()->format('d.m.Y H:i');
        $rows = '';
        $i = 1;

        foreach ($legalActs as $act) {
            $noteText = $act->executionNote?->note ?? '';
            $isExecuted = $noteText && mb_stripos($noteText, 'İcra olunub') !== false;

            $bgColor = '';
            if (!$isExecuted && $act->execution_deadline) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($act->execution_deadline->startOfDay(), false);
                if ($daysLeft < 0) {
                    $bgColor = ' style="background-color:#FFCCCC"';
                } elseif ($daysLeft <= 3) {
                    $bgColor = ' style="background-color:#FFFFCC"';
                }
            }

            $deadlineText = $act->execution_deadline?->format('d.m.Y') ?? '-';
            if (!$isExecuted && $act->execution_deadline) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($act->execution_deadline->startOfDay(), false);
                if ($daysLeft < 0) {
                    $deadlineText .= ' (İcra müddəti bitib)';
                } elseif ($daysLeft <= 3) {
                    $deadlineText .= ' (' . $daysLeft . ' gün qalıb)';
                }
            }

            $rows .= "<tr{$bgColor}>
                <td>{$i}</td>
                <td>" . e($act->actType?->name ?? '-') . "</td>
                <td>" . e($act->legal_act_number ?? '-') . "</td>
                <td>" . ($act->legal_act_date?->format('d.m.Y') ?? '-') . "</td>
                <td>" . e($act->summary ?? '-') . "</td>
                <td>" . e($act->issuingAuthority?->name ?? '-') . "</td>
                <td>" . e($act->executor?->name ?? '-') . "</td>
                <td>" . e($act->task_number ?? '-') . "</td>
                <td>{$deadlineText}</td>
                <td>" . e($noteText ?: '-') . "</td>
                <td>" . e($act->related_document_number ?? '-') . "</td>
                <td>" . ($act->related_document_date?->format('d.m.Y') ?? '-') . "</td>
            </tr>";
            $i++;
        }

        return <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Legal Acts</x:Name>
                    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        td, th { border: 1px solid #333; padding: 4px 8px; font-family: Arial; font-size: 10pt; vertical-align: top; }
        th { background-color: #1e3a5f; color: #ffffff; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Aktın Növü</th>
                <th>Aktın Nömrəsi</th>
                <th>Aktın Tarixi</th>
                <th>Xülasə</th>
                <th>Verən Orqan</th>
                <th>İcraçı</th>
                <th>Tapşırıq Nömrəsi</th>
                <th>İcra Müddəti</th>
                <th>İcra Qeydi</th>
                <th>Əlaqəli Sənəd №</th>
                <th>Əlaqəli Sənəd Tarixi</th>
            </tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>
    <br>
    <table>
        <tr><td><b>Cəmi: {$legalActs->count()} qeyd</b></td></tr>
        <tr><td>Hazırlanma tarixi: {$date}</td></tr>
    </table>
</body>
</html>
HTML;
    }
}
