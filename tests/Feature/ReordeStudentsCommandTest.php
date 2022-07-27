<?php

namespace Tests\Feature;

use App\Console\Commands\ReorderStudentsBySchool;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class ReordeStudentsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $baseUrl = 'api/students';
    protected $headers = [];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testReorderStudents()
    {
        $school = School::factory()->create();
        $student = [];
        for ($numberOfStudents = 0; $numberOfStudents < 4; $numberOfStudents++) {
            $student[$numberOfStudents] = Student::create(
                [
                    'name' => fake()->name,
                    'school_id' => $school->id
                ]
            );
        }
        $this->withHeaders($this->headers)->deleteJson($this->baseUrl . "/" .$student[0]['id'])
            ->assertNoContent();
        $this->assertDatabaseHas('students', [

            'id' => $student[1]['id'],
            'order' => $student[0]['order'],
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', $this->baseUrl
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);

        $this->user = User::factory()->create();
        $token = $this->user->createToken('TestToken')->accessToken;
        $this->headers['Accept'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer ' . $token;
    }
}
