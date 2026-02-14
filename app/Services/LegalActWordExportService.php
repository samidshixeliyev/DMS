<?php

namespace App\Services;

class LegalActWordExportService
{
    public function export($legalActs, $filename = 'legal_acts.docx')
    {
        // Use HTML-based Word document (works without ZipArchive extension)
        $html = $this->generateHtml($legalActs);
        
        // Save to temp file
        $tempFile = storage_path('app/temp/' . $filename);
        
        // Create directory if not exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        // Write as .doc (HTML format that Word can open)
        file_put_contents($tempFile, $html);
        
        return $tempFile;
    }

    protected function generateHtml($legalActs)
    {
        $date = now()->format('d.m.Y H:i');
        $count = $legalActs->count();
        
        $rows = '';
        $i = 1;
        foreach ($legalActs as $act) {
            $actType = e($act->actType?->name ?? '-');
            $actNumber = e($act->legal_act_number ?? '-');
            $actDate = $act->legal_act_date?->format('d.m.Y') ?? '-';
            $summary = e($act->summary ?? '-');
            $authority = e($act->issuingAuthority?->name ?? '-');
            $executor = e($act->executor?->name ?? '-');
            $taskNumber = e($act->task_number ?? '-');
            $taskDesc = e($act->task_description ?? '-');
            $deadline = $act->execution_deadline?->format('d.m.Y') ?? '-';
            $note = e($act->executionNote?->note ?? '-');
            $relDocNum = e($act->related_document_number ?? '-');
            $relDocDate = $act->related_document_date?->format('d.m.Y') ?? '-';
            
            // Row color based on deadline and execution status
            $rowStyle = '';
            $noteText = $act->executionNote?->note ?? '';
            $isExecuted = $noteText && mb_stripos($noteText, 'İcra olunub') !== false;
            
            if (!$isExecuted && $act->execution_deadline) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($act->execution_deadline->startOfDay(), false);
                if ($daysLeft < 0) {
                    $rowStyle = ' style="background-color: #FFCCCC;"'; // Red - overdue
                } elseif ($daysLeft <= 3) {
                    $rowStyle = ' style="background-color: #FFFFCC;"'; // Yellow - 3 days left
                }
            }
            
            $rows .= "
            <tr{$rowStyle}>
                <td>{$i}</td>
                <td>{$actType}</td>
                <td>{$actNumber}</td>
                <td>{$actDate}</td>
                <td>{$summary}</td>
                <td>{$authority}</td>
                <td>{$executor}</td>
                <td>{$taskNumber}</td>
                <td>{$deadline}</td>
                <td>{$note}</td>
                <td>{$relDocNum}</td>
                <td>{$relDocDate}</td>
            </tr>";
            $i++;
        }

        return <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page {
            size: landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        h1 {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 5px;
        }
        .meta {
            text-align: right;
            font-size: 9pt;
            color: #666;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px 6px;
            vertical-align: top;
        }
        th {
            background-color: #1e3a5f;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }
        .total {
            margin-top: 10px;
            font-weight: bold;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <h1>Legal Acts Report</h1>
    <div class="meta">Generated: {$date}</div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sənədin növü</th>
                <th>Sənədin nömrəsi</th>
                <th>Sənədin tarixi</th>
                <th>Summary</th>
                <th>Issuing Authority</th>
                <th>Executor</th>
                <th>Task Number</th>
                <th>Deadline</th>
                <th>Execution Note</th>
                <th>Related Doc #</th>
                <th>Related Doc Date</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
    </table>
    
    <div class="total">Total Records: {$count}</div>
</body>
</html>
HTML;
    }
}