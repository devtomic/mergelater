<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays home page for guests', function () {
    $response = $this->get('/');

    $response->assertOk();
});
