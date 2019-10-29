<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;
use function factory;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_login()
    {
        Date::setTestNow('2019-10-29 23:43:00');

        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password'
        ])->assertRedirect('/home');

        $this->assertDatabaseHas('logins', [
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'created_at' => '2019-10-29 23:43:00'
        ]);
    }

    public function test_can_login_with_username_instead_of_email()
    {
        $user = factory(User::class)->create([
            'username' => 'john_doe',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        $this->post('/login', [
            'username' => 'john_doe',
            'password' => 'password'
        ])->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);
    }
}
