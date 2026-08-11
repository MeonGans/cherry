<?php

namespace Tests\Feature;

use App\Models\Element;
use App\Models\Liceum;
use App\Models\Session as CampSession;
use App\Models\Team;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamVoteOptionsTest extends TestCase
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

    public function test_metal_is_not_available_when_only_four_teams_are_active_in_the_session(): void
    {
        [$vote, $voter, $teams] = $this->createTeamVote(4);

        $this->get(route('votes.vote', ['voteUrl' => $vote->vote_url, 'userId' => $voter->id]))
            ->assertOk()
            ->assertDontSee('value="'.$voter->team_id.'"', false)
            ->assertDontSee('value="'.$teams['Метал']->id.'"', false)
            ->assertSee('value="'.$teams['Повітря']->id.'"', false);

        $this->post(route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $voter->id]), [
            'team_id' => $teams['Метал']->id,
        ])
            ->assertRedirect(route('votes.vote', ['voteUrl' => $vote->vote_url, 'userId' => $voter->id]))
            ->assertSessionHasErrors('message');

        $this->assertDatabaseMissing('user_votes', [
            'vote_id' => $vote->id,
            'user_id' => $voter->id,
        ]);
    }

    public function test_metal_remains_available_when_all_five_teams_are_active(): void
    {
        [$vote, $voter, $teams] = $this->createTeamVote(5);

        $this->get(route('votes.vote', ['voteUrl' => $vote->vote_url, 'userId' => $voter->id]))
            ->assertOk()
            ->assertSee('value="'.$teams['Метал']->id.'"', false);
    }

    private function createTeamVote(int $activeTeamCount): array
    {
        $session = CampSession::factory()->create(['active' => true]);
        $liceum = Liceum::factory()->create();
        $teams = collect(['Вогонь', 'Повітря', 'Вода', 'Земля', 'Метал'])
            ->mapWithKeys(function (string $name) {
                $element = Element::create(['name' => $name, 'color' => '#123456']);
                $team = Team::create(['name' => $name, 'element_id' => $element->id]);

                return [$name => $team];
            });

        $activeTeams = $teams->take($activeTeamCount);
        $users = $activeTeams->map(fn (Team $team) => User::factory()->create([
            'session_id' => $session->id,
            'liceum_id' => $liceum->id,
            'team_id' => $team->id,
        ]));

        $vote = Vote::create([
            'name' => 'Team vote',
            'vote_url' => uniqid('team-vote-', true),
            'type' => Vote::TYPE_TEAM,
            'session_id' => $session->id,
        ]);

        return [$vote, $users->first(), $teams];
    }
}
