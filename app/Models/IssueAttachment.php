<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueAttachment extends Model
{
    use HasFactory;
    public $timestamps = false;
    // Define the table associated with the model
    protected $table = 'issue_attachment';

    // Define the primary key
    protected $primaryKey = 'attachment_id';

    // Disable auto-incrementing if the primary key is not an integer
    public $incrementing = true;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'issue_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'storage_disk',
        'upload_date',
    ];

    // Define relationship with the Issue model
    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'issue_id');
    }
}
