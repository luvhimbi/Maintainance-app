<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping; // Used to map data rows

class StudentsAndStaffExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $students;
    protected $staffMembers;
    protected $totalStudentIssues;
    protected $totalStaffIssues;
    protected $startDate;
    protected $endDate;
    protected $searchTerm;

    public function __construct(array $data)
    {
        $this->students = $data['students'];
        $this->staffMembers = $data['staffMembers'];
        $this->totalStudentIssues = $data['totalStudentIssues'];
        $this->totalStaffIssues = $data['totalStaffIssues'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->searchTerm = $data['searchTerm'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = new Collection();

        // Add a general report summary section at the top
        $data->push(['OCM - Students & Staff Report']);
        $data->push(['Generated On:', now()->format('F j, Y \a\t H:i A T')]);
        if ($this->startDate && $this->endDate) {
            $data->push(['Date Range:', \Carbon\Carbon::parse($this->startDate)->format('F j, Y') . ' to ' . \Carbon\Carbon::parse($this->endDate)->format('F j, Y')]);
        }
        if ($this->searchTerm) {
            $data->push(['Search Term:', $this->searchTerm]);
        }
        $data->push(['']); // Empty row for spacing
        $data->push(['Overall Summary:']);
        $data->push(['Total Students (filtered):', $this->totalStudentIssues]); // Issues, not count
        $data->push(['Total Staff (filtered):', $this->totalStaffIssues]); // Issues, not count
        $data->push(['Total Issues Reported (overall):', $this->totalStudentIssues + $this->totalStaffIssues]);
        $data->push(['']); // Empty row for spacing
        $data->push(['']); // Empty row for spacing

        // Add Students section
        $data->push(['STUDENTS DATA']);
        $data->push($this->headings()); // Add headings for student table
        foreach ($this->students as $student) {
            $data->push([
                $student->first_name . ' ' . $student->last_name,
                $student->email,
                $student->phone_number,
                $student->studentDetail->student_number ?? 'N/A',
                $student->studentDetail->course ?? 'N/A',
                $student->studentDetail->faculty ?? 'N/A',
                'N/A', // Department (for consistency with staff)
                'N/A', // Position (for consistency with staff)
                $student->address,
                $student->issues->count(),
            ]);
        }

        $data->push(['']); // Empty row for spacing
        $data->push(['']); // Empty row for spacing

        // Add Staff Members section
        $data->push(['STAFF MEMBERS DATA']);
        $data->push($this->headings()); // Add headings for staff table (re-use same structure)
        foreach ($this->staffMembers as $staff) {
            $data->push([
                $staff->first_name . ' ' . $staff->last_name,
                $staff->email,
                $staff->phone_number,
                'N/A', // Student Number (for consistency with students)
                'N/A', // Course (for consistency with students)
                'N/A', // Faculty (for consistency with students)
                $staff->staffDetail->department ?? 'N/A',
                $staff->staffDetail->position_title ?? 'N/A',
                $staff->address,
                $staff->issues->count(),
            ]);
        }

        return $data;
    }

    /**
     * Define the column headings for the tables.
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone Number',
            'Student Number',
            'Course',
            'Faculty',
            'Department',
            'Position',
            'Address',
            'Issues Reported',
        ];
    }

    /**
     * Map data to columns (not strictly needed if collection() formats it perfectly, but good practice)
     * This method is called for each row returned by collection().
     */
    public function map($row): array
    {
        return $row;
    }
}
