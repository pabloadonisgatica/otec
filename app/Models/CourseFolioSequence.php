<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFolioSequence extends Model
{
    protected $table = 'course_folio_sequences';

    protected $fillable = ['last_number'];
}

