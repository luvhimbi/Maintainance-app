<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TechnicianReportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['technicians']);
    }

    public function headings(): array
    {
        return [
            'Technician Name',
            'Total Tasks',
            'Completed Tasks',
            'Completion Rate (%)',
            'Average Completion Time (Days)'
        ];
    }
} 