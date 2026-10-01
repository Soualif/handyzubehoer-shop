<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_browser_language(): void
    {
        $this->get('/', ['Accept-Language' => 'de-CH,de;q=0.9,en;q=0.8'])->assertRedirect('/de');
        $this->get('/', ['Accept-Language' => 'fr-CH,fr;q=0.9'])->assertRedirect('/fr');
        $this->get('/', ['Accept-Language' => 'it-IT,it;q=0.9'])->assertRedirect('/it');
        $this->get('/', ['Accept-Language' => 'en-GB'])->assertRedirect('/en');
    }

    public function test_respects_browser_preference_order(): void
    {
        $this->get('/', ['Accept-Language' => 'es-ES,es;q=0.9,fr;q=0.8,de;q=0.7'])->assertRedirect('/fr');
    }

    public function test_other_languages_fall_back_to_english(): void
    {
        $this->get('/', ['Accept-Language' => 'es-ES,pt;q=0.8'])->assertRedirect('/en');
        $this->get('/')->assertRedirect('/en');
    }

    public function test_remembered_choice_wins_over_browser(): void
    {
        $this->withUnencryptedCookie('locale', 'it')
            ->get('/', ['Accept-Language' => 'de-CH'])
            ->assertRedirect('/it');
    }

    public function test_localized_page_is_translated_and_remembered(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('<html lang="fr">', false)
            ->assertSee('Accessoires pour mobiles livrés dans toute la Suisse')
            ->assertCookie('locale', 'fr', false);

        $this->get('/de')->assertSee('Handyzubehör mit Lieferung in die ganze Schweiz');
        $this->get('/it')->assertSee('Accessori per cellulari con consegna in tutta la Svizzera');
        $this->get('/en')->assertSee('Mobile accessories delivered across Switzerland');
    }

    public function test_language_menu_links_to_the_same_page(): void
    {
        $this->get('/fr/info/terms')->assertSee('href="'.url('/de/info/terms').'"', false);
    }

    public function test_unsupported_locale_prefix_is_not_found(): void
    {
        $this->get('/es')->assertNotFound();
    }
}
