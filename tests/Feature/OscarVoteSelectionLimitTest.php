<?php

namespace Tests\Feature;

use App\Models\Liceum;
use App\Models\OscarVote;
use App\Models\Session as CampSession;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OscarVoteSelectionLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The pdo_sqlite extension is required for database feature tests.');
        }

        $_ENV['DB_CONNECTION'] = $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();
    }

    public function test_oscar_can_be_created_and_completed_with_one_actor_choice_when_gender_has_fewer_than_five_users(): void
    {
        [$session, $users] = $this->createSessionUsers(2, 2);
        $nominees = $this->nomineePayload($users->firstWhere('gender', 'male'), $users->firstWhere('gender', 'female'));

        $this->post(route('votes.store'), [
            'name' => 'Small Oscar',
            'type' => Vote::TYPE_OSCAR,
            'oscar_nominees' => $nominees,
        ])->assertRedirect();

        $vote = Vote::where('name', 'Small Oscar')->firstOrFail();
        $this->assertSame($session->id, $vote->session_id);
        $this->assertDatabaseCount('oscar_nominees', 5);

        $voter = $users->first();
        $this->get(route('votes.vote', ['voteUrl' => $vote->vote_url, 'userId' => $voter->id]))
            ->assertOk()
            ->assertSee('data-limit="1" data-key="best_actor"', false)
            ->assertSee('data-limit="1" data-key="best_actress"', false);

        $this->post(route('votes.submitVote', ['voteUrl' => $vote->vote_url, 'userId' => $voter->id]), [
            'oscar_votes' => collect($nominees)->map(fn (array $ids) => [$ids[0]])->all(),
        ])->assertRedirect(route('votes.success'));

        $this->assertSame(5, OscarVote::where('vote_id', $vote->id)->where('user_id', $voter->id)->count());
    }

    public function test_oscar_still_requires_three_actor_nominees_when_gender_has_five_users(): void
    {
        [, $users] = $this->createSessionUsers(5, 1);
        $nominees = $this->nomineePayload($users->firstWhere('gender', 'male'), $users->firstWhere('gender', 'female'));

        $this->post(route('votes.store'), [
            'name' => 'Regular Oscar',
            'type' => Vote::TYPE_OSCAR,
            'oscar_nominees' => $nominees,
        ])
            ->assertSessionHasErrors('oscar_nominees.best_actor');

        $this->assertDatabaseMissing('votes', ['name' => 'Regular Oscar']);
    }

    private function createSessionUsers(int $maleCount, int $femaleCount): array
    {
        $session = CampSession::factory()->create(['active' => true]);
        $liceum = Liceum::factory()->create();

        $users = User::factory()->count($maleCount)->create([
            'session_id' => $session->id,
            'liceum_id' => $liceum->id,
            'team_id' => null,
            'gender' => 'male',
        ])->concat(User::factory()->count($femaleCount)->create([
            'session_id' => $session->id,
            'liceum_id' => $liceum->id,
            'team_id' => null,
            'gender' => 'female',
        ]));

        return [$session, $users];
    }

    private function nomineePayload(User $male, User $female): array
    {
        return [
            'best_camera' => [$male->id],
            'best_editing' => [$female->id],
            'best_actress' => [$female->id],
            'best_actor' => [$male->id],
            'best_director' => [$male->id],
        ];
    }
}
