<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

/**
 * Seeds a published visual landing page with sample agenda table rows.
 * Run: php artisan db:seed --class=LandingPageAgendaDemoSeeder
 */
class LandingPageAgendaDemoSeeder extends Seeder
{
    /**
     * @return list<array{slot: string, activity: string, detail: string}>
     */
    private static function agendaTextToItems(string $multiline): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\r|\n/', trim($multiline)) as $line) {
            if ($line === '') {
                continue;
            }
            $p = explode('|', $line, 3);
            $out[] = [
                'slot' => trim($p[0] ?? ''),
                'activity' => trim($p[1] ?? ''),
                'detail' => trim($p[2] ?? ''),
            ];
        }

        return $out;
    }

    public function run(): void
    {
        LandingPage::updateOrCreate(
            ['slug' => 'demo-canton-agenda'],
            [
                'name' => 'Demo — Canton Fair (sample agenda)',
                'industry' => 'Events / Trips',
                'headline' => 'Sample trip agenda (demo)',
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
                            'agenda_items' => self::agendaTextToItems(LandingPage::defaultDemoAgendaItemsText('zh')),
                        ],
                    ],
                ],
            ]
        );
    }
}
