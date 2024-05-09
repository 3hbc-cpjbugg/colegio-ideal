<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Program;

class UserTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', [
            '--class' => 'DatabaseSeeder'
        ]);

    }

    public function testSimpleListOfUserCanBeRetrieved():void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('api/users?paginate=false');

        $response->assertOk();

        //there are 3 users, 2 users created by seeder and third one that my factory created above
        $this->assertEquals(3, count($response->getData()));

    }

    public function testPaginateListOfUserCanBeRetrieved():void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('api/users');

        $response->assertOk();

        $response->assertJsonStructure([
            'current_page',
            'data',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }

    public function testAKindOfUserListCanBeRetrieved():void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $role = Role::first();

        $response = $this->get('api/users?include=role&role='.$role->name);

        $response->assertOk();

        $this->assertEquals($role->name, $response->getData()->data[0]->role[0]->name);

    }


    public function testAUserCanBeCreated():void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);
        $data   = [
            'role'      => Role::first()->name,
            'username'  => 'username_test',
            'name'      => 'User Test',
            'email'     =>  fake()->unique()->safeEmail(),
            'program_id'    =>  Program::first()->id,
            'password'  => 'password'
        ];
        $response = $this->post('api/users',$data);
        $response->assertStatus(201);
    }

    public function testAUserCanBeUpdated():void
    {
        $user = User::factory()->create();

        $newEmailToUpdated = fake()->unique()->safeEmail();

        Sanctum::actingAs($user, ['*']);
        $data   = [
            'username'  => $user->username,
            'name'      => $user->name,
            'email'     => $newEmailToUpdated,
            'password'  => 'password'
        ];

        $response = $this->put('api/users/'.$user->id,$data);

        $response->assertStatus(200);

        $response->assertJsonFragment(['email' => $newEmailToUpdated]);

    }

    public function testSpecificUserCanBeRetrieved():void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('api/users/'.$user->id);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
                'username',
                'program_id',
            ]
        ]);
    }

    public function testAUserCanBeDeleted(){
        /** @var User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->delete('api/users/'.$user->id);

        $response->assertOk();

        $this->assertDatabaseMissing('users', $user->toArray());

    }
}
