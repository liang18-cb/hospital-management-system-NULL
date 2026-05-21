<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by'
    ];

    public function fileable()
    {
        return $this->morphTo();
    }
}