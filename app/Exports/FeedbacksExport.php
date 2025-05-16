<?php

namespace App\Exports;

use App\Models\Feedback;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FeedbacksExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Feedback::with(['user', 'issue'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Issue Title',
            'User',
            'Rating',
            'Comments',
            'Submitted At'
        ];
    }

    public function map($feedback): array
    {
        return [
            $feedback->id,
            $feedback->issue->issue_type ?? 'n. ',
            $feedback->user->first_name ?? 'n. ' ,
            $feedback->rating,
            $feedback->comments,
            $feedback->created_at->format('Y-m-d H:i:s')
        ];
    }
}