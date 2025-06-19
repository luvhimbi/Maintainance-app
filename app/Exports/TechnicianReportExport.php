<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TechnicianReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles
{
    protected $technicians;
    protected $stats;
    protected $startDate;
    protected $endDate;
    protected $filters;

    public function __construct(array $data)
    {
        $this->technicians = $data['technicians'];
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
        $data->push(['Technician Performance Report']);
        $data->push(['Generated On:', now()->format('F j, Y \a\t H:i A T')]);
        $data->push(['']); // Empty row for spacing

        // Summary
        $data->push(['Report Summary:']);
        $data->push(['Period:', $this->startDate->format('F j, Y') . ' to ' . $this->endDate->format('F j, Y')]);
        $data->push(['Total Technicians (filtered):', $this->stats['total_technicians']]);
        $data->push(['Total Tasks Assigned (filtered):', $this->stats['total_tasks']]);
        $data->push(['Total Tasks Completed (filtered):', $this->stats['completed_tasks']]);
        $data->push(['Average Completion Rate:', number_format($this->stats['avg_completion_rate'], 2) . '%']);
        $data->push(['']); // Empty row for spacing

        // Filters Applied
        $data->push(['Filters Applied:']);
        $data->push(['Status:', ucfirst(str_replace('_', ' ', $this->filters['status']))]);
        $data->push(['Priority:', ucfirst($this->filters['priority'])]);
        $data->push(['']); // Empty row for spacing
        $data->push(['']); // Empty row for spacing

        // Add Technician Details section
        $data->push(['TECHNICIAN DETAILS']);
        $data->push($this->headings()); // Add headings for technician table
        foreach ($this->technicians as $technician) {
            $data->push([
                $technician['name'],
                $technician['email'],
                $technician['phone_number'],
                $technician['address'],
                $technician['specialization'],
                $technician['availability'],
                $technician['workload'],
                $technician['total_tasks'],
                $technician['completed_tasks'],
                number_format($technician['completion_rate'], 2) . '%',
                number_format($technician['avg_completion_time'], 1),
            ]);
        }

        return $data;
    }

    /**
     * Define the column headings for the technician table.
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone Number',
            'Address',
            'Specialization',
            'Availability',
            'Workload',
            'Total Tasks',
            'Completed Tasks',
            'Completion Rate',
            'Avg Completion Time (Days)',
        ];
    }

    /**
     * Map data to columns (optional, but useful if you need to transform cell data).
     */
    public function map($row): array
    {
        return $row;
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Style for the main title
        $sheet->mergeCells('A1:K1'); // Merge cells for the title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Style for generated date
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true);

        // Style for section headers (e.g., "Report Summary:", "Filters Applied:", "TECHNICIAN DETAILS")
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A13')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A18')->getFont()->setBold(true)->setSize(12); // "TECHNICIAN DETAILS"

        // Style for table headers (adjust row number based on content above)
        $headerRow = 19; // Assuming "TECHNICIAN DETAILS" is row 18, then headings are row 19
        $sheet->getStyle('A' . $headerRow . ':K' . $headerRow)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A' . $headerRow . ':K' . $headerRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF5F5F5');
        $sheet->getStyle('A' . $headerRow . ':K' . $headerRow)->getAlignment()->setHorizontal('center');

        // Apply borders to the table data (adjust range as needed)
        $lastRow = $headerRow + $this->technicians->count();
        $sheet->getStyle('A' . $headerRow . ':K' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}
