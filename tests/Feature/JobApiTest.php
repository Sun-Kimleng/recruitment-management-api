<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_if_can_create_job()
    {   
        //login in the the user first
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        //Fill the data form
        $formData = [
            'name'=> 'Netfl',
            'description'=>'etc',
        ];
        

        $this->withoutExceptionHandling();

        // $this->post(route('user.job'), $formData)
        // $this->json('POST', 'user/job', $formData)
        

        $response = $this->withHeaders([
            'accept' => 'application/json',
        ])->postJson('api/user/job', $formData);
 
        $response
            ->assertStatus(201)
            ->assertJson([
                'created' => true,
            ]);
        
        $response->assertStatus((201));
    }
}
