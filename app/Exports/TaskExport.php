<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon; // Ensure Carbon is imported

class TaskExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $tasks;
    protected $stats;
    protected $startDate;
    protected $endDate;
    protected $filters;

    public function __construct(array $data)
    {
        $this->tasks = $data['tasks'];
        $this->stats = $data['stats'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->filters = $data['filters'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = new Collection();

        // Add a general report summary section at the top
        $data->push(['OCM - Online Campus Management']);
        $data->push(['Maintenance Task Report']);
        $data->push(['Generated On:', now()->format('F j, Y \a\t H:i A T')]);
        $data->push(['']); // Empty row for spacing

        // Summary
        $data->push(['Report Summary:']);
        $data->push(['Period:', $this->startDate->format('F j, Y') . ' to ' . $this->endDate->format('F j, Y')]);
        $data->push(['Total Tasks:', $this->stats['total']]);
        $data->push(['Completed Tasks:', $this->stats['completed']]);
        $data->push(['Pending Tasks:', $this->stats['pending']]);
        $data->push(['In Progress Tasks:', $this->stats['in_progress']]);
        $data->push(['Overdue Tasks:', $this->stats['overdue']]);
        $data->push(['']); // Empty row for spacing

        // Filters Applied
        $data->push(['Filters Applied:']);
        $data->push(['Status:', ucfirst(str_replace('_', ' ', $this->filters['status']))]);
        $data->push(['Priority:', ucfirst($this->filters['priority'])]);
        $data->push(['']); // Empty row for spacing
        $data->push(['']); // Empty row for spacing

        // Add Task Details section
        $data->push(['TASK DETAILS']);
        $data->push($this->headings()); // Add headings for task table
        foreach ($this->tasks as $task) {
            $data->push([
                '#' . $task->task_id,
                $task->issue->issue_type ?? 'N/A',
                ($task->issue->location->building_name ?? 'N/A') .
                ($task->issue->location->room_number ? ' (Room ' . $task->issue->location->room_number . ')' : ''),
                $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned',
                ucfirst(str_replace('_', ' ', $task->issue_status)),
                $task->expected_completion->format('M d, Y'),
                $task->expected_completion < now() && $task->issue_status != 'completed' ? 'YES' : 'NO', // Overdue Flag
            ]);
        }

        return $data;
    }

    /**
     * Define the column headings for the task table.
     */
    public function headings(): array
    {
        return [
            'Task ID',
            'Issue Type',
            'Location',
            'Assignee',
            'Status',
            'Due Date',
            'Overdue', // New column
        ];
    }

    /**
     * Map data to columns (optional, but useful if you need to transform cell data).
     */
    public function map($row): array
    {
        return $row;
    }
}
