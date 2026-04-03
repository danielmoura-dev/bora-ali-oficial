<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryField;
use App\Models\Event;
use App\Models\EventFieldValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name'       => 'Festa, Festival ou Show',
            'slug'       => 'festa-festival-show',
            'icon'       => '🎉',
            'is_active'  => true,
            'sort_order' => 1,
        ], $overrides));
    }

    private function makeFieldFor(Category $category, array $overrides = []): CategoryField
    {
        return CategoryField::create(array_merge([
            'category_id' => $category->id,
            'name'        => 'dress_code',
            'label'       => 'Dress Code',
            'type'        => 'text',
            'required'    => false,
            'sort_order'  => 1,
        ], $overrides));
    }

    // ── API de campos dinâmicos ───────────────────────────────

    #[Test]
    public function api_returns_category_with_no_fields(): void
    {
        $this->makeCategory();

        $this->getJson('/api/categories/festa-festival-show/fields')
            ->assertOk()
            ->assertJsonStructure(['category' => ['name', 'icon'], 'fields'])
            ->assertJsonPath('category.name', 'Festa, Festival ou Show')
            ->assertJsonPath('fields', []);
    }

    #[Test]
    public function api_returns_category_fields(): void
    {
        $category = $this->makeCategory([
            'name' => 'Fisiculturismo',
            'slug' => 'fisiculturismo',
            'icon' => '🏋️',
        ]);

        $this->makeFieldFor($category, [
            'name'        => 'federation',
            'label'       => 'Federação responsável',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => 'Ex: IFBB Brasil',
        ]);

        $this->getJson('/api/categories/fisiculturismo/fields')
            ->assertOk()
            ->assertJsonPath('category.name', 'Fisiculturismo')
            ->assertJsonCount(1, 'fields')
            ->assertJsonPath('fields.0.name', 'federation')
            ->assertJsonPath('fields.0.label', 'Federação responsável')
            ->assertJsonPath('fields.0.required', true)
            ->assertJsonPath('fields.0.placeholder', 'Ex: IFBB Brasil');
    }

    #[Test]
    public function api_returns_404_for_unknown_category(): void
    {
        $this->getJson('/api/categories/categoria-inexistente/fields')
            ->assertNotFound();
    }

    #[Test]
    public function api_returns_404_for_inactive_category(): void
    {
        $this->makeCategory(['slug' => 'inativa', 'is_active' => false]);

        $this->getJson('/api/categories/inativa/fields')
            ->assertNotFound();
    }

    // ── Relacionamentos do model ──────────────────────────────

    #[Test]
    public function category_has_many_fields(): void
    {
        $category = $this->makeCategory();
        $this->makeFieldFor($category);
        $this->makeFieldFor($category, ['name' => 'theme', 'label' => 'Tema']);

        $this->assertCount(2, $category->fresh()->fields);
    }

    #[Test]
    public function category_field_has_correct_casts(): void
    {
        $category = $this->makeCategory();
        $field = $this->makeFieldFor($category, [
            'type'    => 'select',
            'options' => ['Opção A', 'Opção B'],
        ]);

        $this->assertIsArray($field->fresh()->options);
        $this->assertContains('Opção A', $field->fresh()->options);
    }

    // ── Campos extras salvos no evento ────────────────────────

    #[Test]
    public function event_field_values_are_saved_on_create(): void
    {
        $user     = User::factory()->create();
        $category = $this->makeCategory([
            'name' => 'Esporte',
            'slug' => 'esporte',
            'icon' => '⚽',
        ]);
        $this->makeFieldFor($category, [
            'name'     => 'federation',
            'label'    => 'Federação',
            'required' => true,
        ]);

        $payload = [
            'title'               => 'Campeonato Regional',
            'description'         => str_repeat('Descrição do campeonato. ', 5),
            'category_id'         => $category->id,
            'venue_name'          => 'Ginásio Municipal',
            'venue_address'       => 'Rua das Palmeiras, 100',
            'city'                => 'Fortaleza',
            'state'               => 'CE',
            'starts_at'           => now()->addDays(15)->format('Y-m-d\TH:i'),
            'ends_at'             => now()->addDays(15)->addHours(8)->format('Y-m-d\TH:i'),
            'is_free'             => false,
            'ticket_nomenclature' => 'inscricao',
            'payment_provider'    => 'mercadopago',
            'payment_mode'        => 'direct',
            'payment_methods'     => ['pix'],
            'fields'              => ['federation' => 'CBCA'],
        ];

        $this->actingAs($user)->post(route('events.store'), $payload);

        $event = Event::where('title', 'Campeonato Regional')->first();

        $this->assertNotNull($event);
        $this->assertEquals($category->id, $event->category_id);

        $value = EventFieldValue::where('event_id', $event->id)->first();
        $this->assertNotNull($value);
        $this->assertEquals('CBCA', $value->value);
    }

    // ── ticketLabel helper ────────────────────────────────────

    #[Test]
    public function ticket_label_returns_ingresso_by_default(): void
    {
        $event = Event::factory()->create(['ticket_nomenclature' => 'ingresso']);

        $this->assertEquals('ingresso', $event->ticketLabel());
        $this->assertEquals('ingressos', $event->ticketLabel(plural: true));
        $this->assertEquals('Ingresso', $event->ticketLabel(capitalize: true));
    }

    #[Test]
    public function ticket_label_returns_inscricao_when_set(): void
    {
        $event = Event::factory()->create(['ticket_nomenclature' => 'inscricao']);

        $this->assertEquals('inscrição', $event->ticketLabel());
        $this->assertEquals('inscrições', $event->ticketLabel(plural: true));
        $this->assertEquals('Inscrição', $event->ticketLabel(capitalize: true));
    }

    // ── Taxa de serviço ───────────────────────────────────────

    #[Test]
    public function absorb_service_fee_defaults_to_false(): void
    {
        $event = Event::factory()->create();
        $this->assertFalse($event->absorb_service_fee);
    }

    #[Test]
    public function event_can_absorb_service_fee(): void
    {
        $event = Event::factory()->create(['absorb_service_fee' => true]);
        $this->assertTrue($event->absorb_service_fee);
    }
}
