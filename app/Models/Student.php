<?php

namespace App\Models;

use App\Jobs\CallReorderStudentsBySchoolCommand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'school_id', 'order'];

    public function school() : BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public static function boot()
    {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::creating(function ($student) {
            $lastStudentOrder = Student::where('school_id',$student->school_id)->orderBy('created_at','desc')->value('order');
            $student->order = $lastStudentOrder ?  ++$lastStudentOrder : 1;
        });

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::deleted(function($student) {
            CallReorderStudentsBySchoolCommand::dispatch($student);
        });
    }
}
