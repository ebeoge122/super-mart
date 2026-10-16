<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccountRouteTest extends TestCase
{

    public function test_guest_sees_login_view_on_account_route(): void
    {
        $response = $this->get('/account');

        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    public function test_logged_in_user_sees_account_information_view(): void
    {
        $this->withSession([
            'user_id' => 42,
            'user_name' => 'Taylor',
            'user_email' => 'taylor@example.com',
        ]);

        $response = $this->get('/account');

        $response->assertStatus(200);
        $response->assertViewIs('account-info');
        $response->assertViewHas('user', function ($viewUser) {
            return $viewUser->id === 42 && $viewUser->name === 'Taylor';
        });
    }
}
