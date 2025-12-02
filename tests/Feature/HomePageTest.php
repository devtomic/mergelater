<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to login page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
