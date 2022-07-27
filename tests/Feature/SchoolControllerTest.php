<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class SchoolControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $baseUrl = 'api/schools';
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
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            );
    }

    public function testSchoolIsCreatedSuccessfully()
    {

        $payload = [
            'name' => fake()->name
        ];
        $this->withHeaders($this->headers)->postJson($this->baseUrl, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ]
                ]
            );
        $this->assertDatabaseHas('schools', $payload);
    }

    public function testSchoolIsShownCorrectly()
    {
        $school = School::create(
            [
                'name' => fake()->name
            ]
        );

        $this->withHeaders($this->headers)->getJson($this->baseUrl . "/$school->id")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $school->id,
                        'name' => $school->name,
                        'created_at' => (string)$school->created_at,
                        'updated_at' => (string)$school->updated_at,
                    ]
                ]
            );
    }

    public function testSchoolIsDestroyed()
    {

        $schoolData =
            [
                'name' => fake()->name
            ];
        $school = School::create(
            $schoolData
        );

        $this->withHeaders($this->headers)->deleteJson($this->baseUrl . "/$school->id")
            ->assertNoContent();
        $this->assertSoftDeleted('schools', $schoolData);
    }

    public function testUpdateSchoolReturnsCorrectData()
    {
        $school = School::create(
            [
                'name' => fake()->name
            ]
        );

        $payload = [
            'name' => fake()->name
        ];

        $this->withHeaders($this->headers)->patchJson($this->baseUrl . "/$school->id", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $school->id,
                        'name' => $payload['name'],
                        'created_at' => (string)$school->created_at,
                        'updated_at' => (string)$school->updated_at,
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
