<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function event_generates_unique_slug_from_title(): void
    {
        $user = User::factory()->create();

        $slug1 = Event::generateSlug('Show de Rock');
        $slug2 = Event::generateSlug('Show de Rock');

        Event::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Show de Rock',
            'slug'    => $slug1,
        ]);

        $slug2 = Event::generateSlug('Show de Rock');

        $this->assertNotEquals($slug1, $slug2);
        $this->assertStringStartsWith('show-de-rock', $slug2);
    }

    #[Test]
    public function event_is_current_when_starts_in_future(): void
    {
        $event = Event::factory()->make([
            'starts_at' => now()->addDays(3),
            'ends_at'   => now()->addDays(3)->addHours(4),
        ]);

        $this->assertTrue($event->isCurrent());
        $this->assertFalse($event->isFinished());
    }

    #[Test]
    public function event_is_finished_when_ends_in_past(): void
    {
        $event = Event::factory()->make([
            'starts_at' => now()->subDays(5),
            'ends_at'   => now()->subDays(4),
        ]);

        $this->assertTrue($event->isFinished());
        $this->assertFalse($event->isCurrent());
    }

    #[Test]
    public function event_belongs_to_organizer(): void
    {
        $user  = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $event->organizer->id);
    }
}