<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaskReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['tasks'];
    }

    public function headings(): array
    {
        return [
            'Task ID',
            'Description',
            'Priority',
            'Status',
            'Assigned To',
            'Expected Completion'
        ];
    }

    public function map($task): array
    {
        return [
            $task->task_id,
            $task->issue->issue_description,
            $task->priority,
            $task->issue_status,
            $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned',
            $task->expected_completion ? \Carbon\Carbon::parse($task->expected_completion)->format('M d, Y') : 'Not set'
        ];
    }
} 