<?php

namespace App\Services;

use App\Exceptions\GitHubAccessDeniedException;
use App\Exceptions\GitHubMergeException;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    public function __construct(
        private string $token,
    ) {}

    public function getPullRequest(string $owner, string $repo, int $pullNumber): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/vnd.github+json',
        ])->get("https://api.github.com/repos/{$owner}/{$repo}/pulls/{$pullNumber}");

        if ($response->status() === 404) {
            return null;
        }

        if ($response->status() === 403) {
            throw new GitHubAccessDeniedException('You don\'t have access to this repository.');
        }

        return $response->json();
    }

    public function mergePullRequest(string $owner, string $repo, int $pullNumber, string $mergeMethod): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/vnd.github+json',
        ])->put("https://api.github.com/repos/{$owner}/{$repo}/pulls/{$pullNumber}/merge", [
            'merge_method' => $mergeMethod,
        ]);

        if ($response->failed()) {
            throw new GitHubMergeException($response->json('message', 'Failed to merge pull request'));
        }

        return $response->json();
    }
}
