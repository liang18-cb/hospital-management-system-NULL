<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use SoftDeletes;

    protected $table = 'files';

    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'file_path',
        'file_name',
        'file_type',
        'uploaded_by'
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}