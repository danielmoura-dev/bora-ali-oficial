<?php

namespace Tests\Feature;

use App\Mail\EventReminderMail;
use App\Mail\OrderConfirmedMail;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): array
    {
        $organizer = User::factory()->create();
        $event     = Event::factory()->current()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
            'title'   => 'Festival de Verão',
        ]);
        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create(['ticket_type_id' => $type->id]);
        $buyer = User::factory()->create(['email' => 'comprador@exemplo.com']);
        $order = Order::factory()->paid()->create([
            'user_id'  => $buyer->id,
            'event_id' => $event->id,
            'total'    => 5100,
        ]);
        $item = OrderItem::create([
            'order_id'        => $order->id,
            'ticket_batch_id' => $batch->id,
            'ticket_type_id'  => $type->id,
            'quantity'        => 1,
            'unit_price'      => 5000,
            'subtotal'        => 5000,
            'ticket_code'     => 'ABCD-1234',
        ]);

        return compact('organizer', 'event', 'buyer', 'order', 'item');
    }

    #[Test]
    public function order_confirmed_mail_is_sent_after_payment(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        app(\App\Services\OrderService::class)
            ->confirmPayment($order, 'pix');

        Mail::assertSent(OrderConfirmedMail::class, function ($mail) use ($buyer, $order) {
            return $mail->hasTo($buyer->email)
                && $mail->order->id === $order->id;
        });
    }

    #[Test]
    public function order_confirmed_mail_contains_ticket_code(): void
    {
        Mail::fake();

        ['order' => $order, 'item' => $item] = $this->makeOrder();

        $mailable = new OrderConfirmedMail($order->load(['items.ticketType', 'event']));

        $mailable->assertSeeInHtml('ABCD-1234');
        $mailable->assertSeeInHtml('Festival de Verão');
    }

    #[Test]
    public function event_reminder_mail_is_queued_for_upcoming_events(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'event' => $event, 'order' => $order] = $this->makeOrder();

        // Simula envio do lembrete
        Mail::to($buyer->email)->send(new EventReminderMail($event, $order));

        Mail::assertSent(EventReminderMail::class, function ($mail) use ($buyer) {
            return $mail->hasTo($buyer->email);
        });
    }

    #[Test]
    public function event_reminder_mail_contains_event_details(): void
    {
        ['event' => $event, 'order' => $order] = $this->makeOrder();

        $mailable = new EventReminderMail($event, $order);

        $mailable->assertSeeInHtml('Festival de Verão');
        $mailable->assertSeeInHtml($event->venue_name);
    }

    #[Test]
    public function reminder_command_sends_to_buyers_of_upcoming_events(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'event' => $event] = $this->makeOrder();

        // Evento começa amanhã
        $event->forceFill([
            'starts_at' => now()->addDay(),
            'ends_at'   => now()->addDay()->addHours(4),
        ])->save();

        $this->artisan('events:send-reminders')->assertExitCode(0);

        Mail::assertSent(EventReminderMail::class, function ($mail) use ($buyer) {
            return $mail->hasTo($buyer->email);
        });
    }
}