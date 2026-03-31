<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Order;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    protected $signature   = 'events:send-reminders';
    protected $description = 'Envia lembretes para compradores de eventos que começam amanhã';

    public function handle(): int
    {
        // Eventos que começam entre 23h e 25h a partir de agora
        $events = Event::published()
            ->whereBetween('starts_at', [
                now()->addHours(23),
                now()->addHours(25),
            ])
            ->with(['orders' => fn ($q) => $q->where('status', 'paid')
                ->with(['user', 'items.ticketType'])])
            ->get();

        $count = 0;

        foreach ($events as $event) {
            foreach ($event->orders as $order) {
                Mail::to($order->user->email)
                    ->send(new EventReminderMail($event, $order));
                $count++;
            }
        }

        $this->info("Lembretes enviados: {$count}");

        return self::SUCCESS;
    }
}