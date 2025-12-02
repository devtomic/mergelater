<?php

use App\Services\GitHubService;
use Illuminate\Support\Facades\Http;

it('can be instantiated with a token', function () {
    $service = new GitHubService('test-token');

    expect($service)->toBeInstanceOf(GitHubService::class);
});

it('can get pull request details', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123' => Http::response([
            'number' => 123,
            'state' => 'open',
            'title' => 'Test PR',
            'mergeable' => true,
        ], 200),
    ]);

    $service = new GitHubService('test-token');
    $pr = $service->getPullRequest('owner', 'repo', 123);

    expect($pr['number'])->toBe(123);
    expect($pr['state'])->toBe('open');
    expect($pr['title'])->toBe('Test PR');

    Http::assertSent(function ($request) {
        return $request->hasHeader('Authorization', 'Bearer test-token')
            && $request->url() === 'https://api.github.com/repos/owner/repo/pulls/123';
    });
});

it('can merge a pull request', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'sha' => 'abc123',
            'merged' => true,
            'message' => 'Pull Request successfully merged',
        ], 200),
    ]);

    $service = new GitHubService('test-token');
    $result = $service->mergePullRequest('owner', 'repo', 123, 'squash');

    expect($result['merged'])->toBeTrue();
    expect($result['sha'])->toBe('abc123');

    Http::assertSent(function ($request) {
        return $request->method() === 'PUT'
            && $request->url() === 'https://api.github.com/repos/owner/repo/pulls/123/merge'
            && $request['merge_method'] === 'squash';
    });
});

it('throws exception when merge fails', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'message' => 'Pull Request is not mergeable',
        ], 405),
    ]);

    $service = new GitHubService('test-token');
    $service->mergePullRequest('owner', 'repo', 123, 'squash');
})->throws(\App\Exceptions\GitHubMergeException::class, 'Pull Request is not mergeable');

it('throws exception when authentication fails', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/123/merge' => Http::response([
            'message' => 'Bad credentials',
        ], 401),
    ]);

    $service = new GitHubService('invalid-token');
    $service->mergePullRequest('owner', 'repo', 123, 'squash');
})->throws(\App\Exceptions\GitHubMergeException::class, 'Bad credentials');

it('returns null when PR is not found', function () {
    Http::fake([
        'api.github.com/repos/owner/repo/pulls/999' => Http::response([
            'message' => 'Not Found',
        ], 404),
    ]);

    $service = new GitHubService('test-token');
    $result = $service->getPullRequest('owner', 'repo', 999);

    expect($result)->toBeNull();
});

it('throws exception when user lacks repository access', function () {
    Http::fake([
        'api.github.com/repos/owner/private-repo/pulls/123' => Http::response([
            'message' => 'Not Found',
        ], 403),
    ]);

    $service = new GitHubService('test-token');
    $service->getPullRequest('owner', 'private-repo', 123);
})->throws(\App\Exceptions\GitHubAccessDeniedException::class);
