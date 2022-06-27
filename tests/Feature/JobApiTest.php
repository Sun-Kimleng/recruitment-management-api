<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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

        $this ->actingAs($user, 'sanctum', 'verified');
        //Fill the data form
        $formData = [
            'name'=> 'Netfl',
            'description'=>'etc',
        ];
        
        // $this->post(route('user.job'), $formData)
        // $this->json('POST', 'user/job', $formData)      

        $response = $this->withHeaders([
            'accept' => 'application/json',
        ])->postJson('api/user/job', $formData);
            

        $response
            ->assertStatus(200)
            ->assertJson(
                ['message'=>'Succesful added']
            );

    }
}
