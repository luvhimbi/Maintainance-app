<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TechnicianPerformanceExport implements FromCollection, WithHeadings, WithMapping
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
            'Specialization',
            'Total Tasks',
            'Completed Tasks',
            'Completion Rate (%)',
            'Average Completion Time (Days)',
        ];
    }

    public function map($technician): array
    {
        return [
            $technician['first_name'] . ' ' . $technician['last_name'],
            $technician['specialization'] ?? 'Not specified',
            $technician['total_tasks'],
            $technician['completed_tasks'],
            $technician['completion_rate'] . '%',
            $technician['avg_completion_time'] ?? 'N/A',
        ];
    }
}
