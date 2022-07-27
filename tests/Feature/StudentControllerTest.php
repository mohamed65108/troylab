<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $baseUrl = 'api/students';
    protected $headers = [];

    public function testIndexReturnsDataInValidFormat()
    {

        $this->withHeaders($this->headers)->getJson($this->baseUrl)
            ->assertStatus(200)
            ->assertJsonStructure(
                [

                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'school_id',
                            'order',
                            'created_at',
                            'updated_at',
                            'school' => [
                                'id',
                                'name',
                                'created_at',
                                'updated_at',
                            ]
                        ]
                    ]
                ]
            );
    }

    public function testStudentIsCreatedSuccessfully()
    {
        $school = School::create(['name' => fake()->name]);

        $payload = [
            'name' => fake()->name,
            'school_id' => $school->id
        ];
        $this->withHeaders($this->headers)->postJson($this->baseUrl, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'school_id',
                        'order',
                        'created_at',
                        'updated_at',
                        'school' => [
                            'id',
                            'name',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            );
        $this->assertDatabaseHas('students', $payload);
    }

    public function testStudentIsShownCorrectly()
    {

        $school = School::create(
            [
                'name' => fake()->name
            ]
        );

        $student = Student::create(
            [
                'name' => fake()->name,
                'school_id' => $school->id
            ]
        );

        $this->withHeaders($this->headers)->getJson($this->baseUrl . "/$student->id")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'school_id' => $school->id,
                        'order' => $student->order,
                        'created_at' => (string)$student->created_at,
                        'updated_at' => (string)$student->updated_at,
                        'school' => [
                            'id' => $school->id,
                            'name' => $school->name,
                            'created_at' => (string)$school->created_at,
                            'updated_at' => (string)$school->updated_at,
                        ]
                    ]
                ]
            );
    }

    public function testStudentIsDestroyed()
    {

        $school = School::create(['name' => fake()->name]);
        $studentData =
            [
                'name' => fake()->name,
                'school_id' => $school->id
            ];
        $student = Student::create(
            $studentData
        );

        $this->withHeaders($this->headers)->deleteJson($this->baseUrl . "/$student->id")
            ->assertNoContent();
        $this->assertSoftDeleted('students', $studentData);
    }

    public function testUpdateStudentReturnsCorrectData()
    {
        $school = School::create(['name' => fake()->name]);
        $studentData =
            [
                'name' => fake()->name,
                'school_id' => $school->id
            ];
        $student = Student::create(
            $studentData
        );

        $payload = [
            'name' => fake()->name
        ];

        $this->withHeaders($this->headers)->patchJson($this->baseUrl . "/$student->id", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $student->id,
                        'name' => $payload['name'],
                        'school_id' => $school->id,
                        'order' => $student->order,
                        'created_at' => (string)$student->created_at,
                        'updated_at' => (string)$student->updated_at,
                        'school' => [
                            'id' => $school->id,
                            'name' => $school->name,
                            'created_at' => (string)$school->created_at,
                            'updated_at' => (string)$school->updated_at,
                        ]
                    ]
                ]
            );
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
