<?php

it('command exists', function () {
    $this->artisan('app:cache-version')
        ->assertExitCode(0);
});

it('creates a .version file', function () {
    $versionFile = base_path('.version');

    // Ensure clean state
    if (file_exists($versionFile)) {
        unlink($versionFile);
    }

    $this->artisan('app:cache-version')
        ->assertExitCode(0);

    expect(file_exists($versionFile))->toBeTrue();

    // Clean up
    unlink($versionFile);
});

it('writes the git version to the file', function () {
    $versionFile = base_path('.version');

    // Ensure clean state
    if (file_exists($versionFile)) {
        unlink($versionFile);
    }

    $this->artisan('app:cache-version')
        ->assertExitCode(0);

    $content = trim(file_get_contents($versionFile));
    expect($content)->not->toBeEmpty();

    // Clean up
    unlink($versionFile);
});

it('includes version and 8-character commit hash', function () {
    $versionFile = base_path('.version');

    if (file_exists($versionFile)) {
        unlink($versionFile);
    }

    $this->artisan('app:cache-version')
        ->assertExitCode(0);

    $content = trim(file_get_contents($versionFile));

    // Format: "v1.0.0 fa51a2a3" or "dev fa51a2a3"
    expect($content)->toMatch('/^(v[\d.]+|dev) [a-f0-9]{8}$/');

    unlink($versionFile);
});

it('uses provided --tag option instead of git tag', function () {
    $versionFile = base_path('.version');

    if (file_exists($versionFile)) {
        unlink($versionFile);
    }

    $this->artisan('app:cache-version', ['--tag' => '2.0.0'])
        ->assertExitCode(0);

    $content = trim(file_get_contents($versionFile));

    // Should use the provided tag with the commit hash
    expect($content)->toMatch('/^2\.0\.0 [a-f0-9]{8}$/');

    unlink($versionFile);
});
