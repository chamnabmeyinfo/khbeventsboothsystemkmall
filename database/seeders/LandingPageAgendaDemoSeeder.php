<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

/**
 * Seeds the current Canton Fair visual landing page (agenda table rows).
 * Run: php artisan db:seed --class=LandingPageAgendaDemoSeeder
 *
 * Legacy slug demo-canton-agenda is renamed to canton-fair only if canton-fair does not already exist.
 */
class LandingPageAgendaDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! LandingPage::query()->where('slug', 'canton-fair')->exists()) {
            LandingPage::query()->where('slug', 'demo-canton-agenda')->update(['slug' => 'canton-fair']);
        }

        LandingPage::updateOrCreate(
            ['slug' => 'canton-fair'],
            [
                'name' => 'Canton Fair — KHB Events',
                'industry' => 'Events / Trips',
                'headline' => 'Canton Fair business trip',
                'html_content' => '',
                'css_content' => '',
                'js_content' => '',
                'redirect_url' => '/login',
                'show_once_mode' => 'cookie_once',
                'allow_inline_scripts' => true,
                'use_visual_builder' => true,
                'template_key' => 'canton_fair_visual',
                'default_locale' => 'en',
                'enabled_locales' => ['en', 'km', 'zh'],
                'priority' => 500,
                'is_published' => true,
                'is_active' => false,
                'published_at' => now(),
                'visual_content' => [
                    'hero_cta_target' => '/login',
                    'i18n' => [
                        'en' => [
                            'agenda_title' => 'Trip agenda',
                            'agenda_hdr_slot' => 'Time / slot',
                            'agenda_hdr_activity' => 'Activity',
                            'agenda_hdr_detail' => 'Details',
                            'agenda_items' => LandingPage::defaultDemoAgendaItems(),
                        ],
                        'km' => [
                            'agenda_title' => 'កាលវិភាគដំណើរ',
                            'agenda_items' => self::agendaTextToItems(LandingPage::defaultDemoAgendaItemsText('km')),
                        ],
                        'zh' => [
                            'agenda_title' => '行程表',
                            'agenda_items' => LandingPage::parseAgendaItemsFromText(LandingPage::defaultDemoAgendaItemsText('zh')),
                        ],
                    ],
                ],
            ]
        );
    }
}
