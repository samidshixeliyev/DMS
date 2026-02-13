<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

class LegalActWordExportService
{
    public function export($legalActs, $filename = 'legal_acts.docx')
    {
        $phpWord = new PhpWord();
        
        // Add document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('DMS Application');
        $properties->setTitle('Legal Acts Report');
        
        // Create section
        $section = $phpWord->addSection();
        
        // Add title
        $section->addText(
            'Legal Acts Report',
            ['name' => 'Arial', 'size' => 16, 'bold' => true],
            ['alignment' => 'center']
        );
        
        $section->addTextBreak(1);
        
        // Add date
        $section->addText(
            'Generated: ' . now()->format('Y-m-d H:i:s'),
            ['name' => 'Arial', 'size' => 10],
            ['alignment' => 'right']
        );
        
        $section->addTextBreak(1);
        
        // Add table
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'width' => 100 * 50,
            'unit' => 'pct'
        ]);
        
        // Add header row
        $table->addRow(400);
        $table->addCell(1000)->addText('ID', ['bold' => true]);
        $table->addCell(2000)->addText('Document Number', ['bold' => true]);
        $table->addCell(2000)->addText('Document Date', ['bold' => true]);
        $table->addCell(4000)->addText('Title', ['bold' => true]);
        $table->addCell(2500)->addText('Issuing Authority', ['bold' => true]);
        $table->addCell(2000)->addText('Executor', ['bold' => true]);
        $table->addCell(2000)->addText('Category', ['bold' => true]);
        $table->addCell(1500)->addText('Status', ['bold' => true]);
        
        // Add data rows
        foreach ($legalActs as $legalAct) {
            $table->addRow();
            $table->addCell(1000)->addText($legalAct->id);
            $table->addCell(2000)->addText($legalAct->document_number ?? '-');
            $table->addCell(2000)->addText($legalAct->document_date?->format('Y-m-d') ?? '-');
            $table->addCell(4000)->addText($legalAct->title ?? '-');
            $table->addCell(2500)->addText($legalAct->issuingAuthority?->name ?? '-');
            $table->addCell(2000)->addText($legalAct->executor?->name ?? '-');
            $table->addCell(2000)->addText($legalAct->category?->name ?? '-');
            $table->addCell(1500)->addText($legalAct->status?->name ?? '-');
        }
        
        $section->addTextBreak(2);
        $section->addText(
            'Total Records: ' . $legalActs->count(),
            ['bold' => true]
        );
        
        // Save to temp file
        $tempFile = storage_path('app/temp/' . $filename);
        
        // Create directory if not exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);
        
        return $tempFile;
    }
}
