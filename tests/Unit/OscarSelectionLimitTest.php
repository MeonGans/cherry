<?php

namespace Tests\Unit;

use App\Models\Vote;
use PHPUnit\Framework\TestCase;

class OscarSelectionLimitTest extends TestCase
{
    public function test_gender_nomination_uses_one_choice_below_five_participants(): void
    {
        $this->assertSame(1, Vote::oscarSelectionLimit(Vote::OSCAR_NOMINATIONS['best_actor'], 2));
        $this->assertSame(1, Vote::oscarSelectionLimit(Vote::OSCAR_NOMINATIONS['best_actress'], 4));
    }

    public function test_gender_nomination_uses_three_choices_from_five_participants(): void
    {
        $this->assertSame(3, Vote::oscarSelectionLimit(Vote::OSCAR_NOMINATIONS['best_actor'], 5));
        $this->assertSame(3, Vote::oscarSelectionLimit(Vote::OSCAR_NOMINATIONS['best_actress'], 8));
    }

    public function test_non_gender_nomination_keeps_its_configured_limit(): void
    {
        $this->assertSame(1, Vote::oscarSelectionLimit(Vote::OSCAR_NOMINATIONS['best_camera'], 0));
    }
}
