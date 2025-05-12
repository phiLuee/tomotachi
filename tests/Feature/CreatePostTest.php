<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     *  Test for user with 'admin' role creating a post.
     */
    public function test_admin_can_create_post()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/posts', [
            'content' => 'Dies ist ein Testpost.',
        ]);

        $response->assertStatus(302); // oder 201/200 je nach Logik
        $this->assertDatabaseHas('posts', [
            'content' => 'Dies ist ein Testpost.',
        ]);
    }

    /**
     * Test for user with 'user' role creating a post.
     */
    // public function test_user_without_permission_cannot_create_post()
    // {
    //     /** @var \App\Models\User $user */
    //     $user = User::factory()->create(); // Ohne Rolle 

    //     $response = $this->actingAs($user)->post('/posts', [
    //         'content' => 'Unerlaubter Post',
    //     ]);

    //     $response->assertForbidden(); // oder 403
    //     $this->assertDatabaseMissing('posts', [
    //         'content' => 'Unerlaubter Post',
    //     ]);
    // }
}
