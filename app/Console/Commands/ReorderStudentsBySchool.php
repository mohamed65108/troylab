<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\User;
use App\Notifications\NotifyUserAfterReorderStudentsBySchoolCommand;
use Illuminate\Console\Command;

class ReorderStudentsBySchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reorder_students_by_school{studentId} {schoolId} {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reorder Students By School';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Student::where('school_id', $this->arguments()['schoolId'])->where('id', '>', $this->arguments()['studentId'])->decrement('order');
        User::find($this->arguments()['userId'])->notify(new NotifyUserAfterReorderStudentsBySchoolCommand());
    }
}
