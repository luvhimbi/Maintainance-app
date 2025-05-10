<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PerformanceReportExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            'Task Completion' => new TaskCompletionSheet($this->data['performance_data']['task_completion']),
            'Priority Distribution' => new PriorityDistributionSheet($this->data['performance_data']['priority_distribution']),
            'Technician Performance' => new TechnicianPerformanceSheet($this->data['performance_data']['technician_performance'])
        ];
    }
}

class TaskCompletionSheet implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Total Tasks',
            'Completed Tasks'
        ];
    }
}

class PriorityDistributionSheet implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Priority',
            'Count'
        ];
    }
}

class TechnicianPerformanceSheet implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Technician Name',
            'Total Tasks',
            'Completion Rate (%)'
        ];
    }
} 