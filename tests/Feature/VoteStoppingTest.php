<?php

namespace Tests\Feature;

use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteStoppingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The pdo_sqlite extension is required for database feature tests.');
        }

        $_ENV['DB_CONNECTION'] = $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();
    }

    public function test_admin_can_stop_any_vote_type(): void
    {
        foreach ([Vote::TYPE_TEAM, Vote::TYPE_PHOTO, Vote::TYPE_OSCAR] as $type) {
            $vote = $this->createVote($type);

            $this->withSession(['admin_logged_in' => true])
                ->patch(route('votes.stop', $vote->vote_url))
                ->assertRedirect(route('votes.index'));

            $this->assertNotNull($vote->fresh()->stopped_at);
        }
    }

    public function test_stopped_vote_does_not_accept_authentication_or_direct_submission(): void
    {
        $vote = $this->createVote(Vote::TYPE_TEAM, now());

        $this->get(route('votes.show', $vote->vote_url))
            ->assertOk()
            ->assertSee('Голосування завершено')
            ->assertDontSee('name="pin_code"', false);

        $this->post(route('votes.authenticate', $vote->vote_url), ['pin_code' => '1234'])
            ->assertRedirect(route('votes.show', $vote->vote_url))
            ->assertSessionHasErrors('message');

        $this->post(route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => 999]), ['team_id' => 1])
            ->assertRedirect(route('votes.show', $vote->vote_url))
            ->assertSessionHasErrors('message');
    }

    private function createVote(string $type, $stoppedAt = null): Vote
    {
        return Vote::create([
            'name' => 'Test '.$type,
            'vote_url' => uniqid('vote-', true),
            'type' => $type,
            'stopped_at' => $stoppedAt,
        ]);
    }
}
