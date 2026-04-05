<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Machine translation for landing page visual i18n fields (MyMemory free API).
 * For production reliability, consider a paid provider and configure via env.
 */
class LandingTextTranslationService
{
    private const MYMEMORY_URL = 'https://api.mymemory.translated.net/get';

    private const MAX_CHUNK_CHARS = 450;

    /**
     * All visual i18n fields that can be machine-translated (admin form / API).
     *
     * @return list<string>
     */
    public static function visualI18nFieldKeys(): array
    {
        return [
            'hero_title', 'hero_cta_text', 'hero_subtitle',
            'about_title', 'package_title',
            'about_text_en', 'about_text_kh',
            'package_price', 'promotion_section_title', 'promotion_tier_subtitle', 'promotion_tier_offer_template', 'booking_title', 'faq_title', 'terms_title', 'agenda_title',
            'agenda_hdr_slot', 'agenda_hdr_activity', 'agenda_hdr_detail',
            'trip_section_title', 'per_person_label', 'seats_left_suffix',
            'trip_phase_register_cta', 'trip_phase_modal_title',
            'trip_activity_gallery_title', 'trip_activity_gallery_slides_text',
            'hero_stats_text', 'package_items_text', 'trip_dates_text', 'agenda_items_text', 'faq_items_text', 'contact_phones_text', 'terms_text',
            'booking_name_placeholder', 'booking_email_placeholder', 'booking_phone_placeholder',
            'booking_trip_placeholder', 'booking_submit_text',
            // Structured bundles (JSON string over the wire; matches admin trip phases / promotion form slots)
            'trip_phases', 'promotion_discounts',
        ];
    }

    public function translateVisualField(string $text, string $fieldKey, string $sourceLocale, string $targetLocale): string
    {
        if (trim($text) === '') {
            return '';
        }
        if ($sourceLocale === $targetLocale) {
            return $text;
        }

        return match ($fieldKey) {
            'hero_stats_text' => $this->translateHeroStatsPipeLines($text, $sourceLocale, $targetLocale),
            'trip_dates_text', 'faq_items_text', 'agenda_items_text' => $this->translatePipeDelimitedMultiline($text, $sourceLocale, $targetLocale),
            'package_items_text' => $this->translatePackageItemsLines($text, $sourceLocale, $targetLocale),
            'trip_activity_gallery_slides_text' => $this->translateTripActivityGallerySlides($text, $sourceLocale, $targetLocale),
            'contact_phones_text' => $this->translateContactPhones($text, $sourceLocale, $targetLocale),
            'trip_phases' => $this->translateTripPhasesJsonBlob($text, $sourceLocale, $targetLocale),
            'promotion_discounts' => $this->translatePromotionDiscountsJsonBlob($text, $sourceLocale, $targetLocale),
            'promotion_tier_offer_template' => $this->translatePromotionTierOfferTemplate($text, $sourceLocale, $targetLocale),
            default => $this->translatePlain($text, $sourceLocale, $targetLocale),
        };
    }

    /**
     * @param  string  $json  JSON array of phase objects (same shape as stored visual trip_phases)
     */
    private function translateTripPhasesJsonBlob(string $json, string $source, string $target): string
    {
        $trim = trim($json);
        if ($trim === '') {
            return '[]';
        }
        $data = json_decode($trim, true);
        if (! is_array($data)) {
            return $json;
        }
        $out = [];
        foreach ($data as $ph) {
            if (! is_array($ph)) {
                continue;
            }
            $subsOut = [];
            $subs = $ph['subsections'] ?? [];
            if (is_array($subs)) {
                foreach ($subs as $s) {
                    if (! is_array($s)) {
                        continue;
                    }
                    $t = trim((string) ($s['title'] ?? ''));
                    $d = trim((string) ($s['detail'] ?? ''));
                    $subsOut[] = [
                        'title' => $t !== '' ? $this->translateSegment($t, $source, $target) : '',
                        'detail' => $d !== '' ? $this->translatePlain($d, $source, $target) : '',
                    ];
                }
            }
            $label = trim((string) ($ph['label'] ?? ''));
            $date = trim((string) ($ph['date'] ?? ''));
            $status = trim((string) ($ph['status'] ?? ''));
            $seats = trim((string) ($ph['seats_left'] ?? ''));
            $intro = trim((string) ($ph['intro'] ?? ''));
            $fi = trim((string) ($ph['feature_image'] ?? ''));
            $row = [
                'label' => $label !== '' ? $this->translateSegment($label, $source, $target) : '',
                'date' => $date !== '' ? $this->translateSegment($date, $source, $target) : '',
                'status' => $status !== '' ? $this->translateSegment($status, $source, $target) : '',
                'seats_left' => $seats !== '' ? $this->translateSegment($seats, $source, $target) : '',
                'intro' => $intro !== '' ? $this->translatePlain($intro, $source, $target) : '',
                'subsections' => $subsOut,
            ];
            if ($fi !== '') {
                $row['feature_image'] = $fi;
            }
            $out[] = $row;
        }
        $enc = json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $enc !== false ? $enc : $json;
    }

    /**
     * @param  string  $json  JSON object base_price_text, intro_text, tiers[{participants, off_each, label}]
     */
    private function translatePromotionDiscountsJsonBlob(string $json, string $source, string $target): string
    {
        $trim = trim($json);
        if ($trim === '') {
            return '{}';
        }
        $data = json_decode($trim, true);
        if (! is_array($data)) {
            return $json;
        }
        $base = trim((string) ($data['base_price_text'] ?? ''));
        $intro = trim((string) ($data['intro_text'] ?? ''));
        $tiersOut = [];
        $tiers = $data['tiers'] ?? [];
        if (is_array($tiers)) {
            foreach ($tiers as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $n = (int) ($row['participants'] ?? 0);
                $off = (int) ($row['off_each'] ?? 0);
                $label = trim((string) ($row['label'] ?? ''));
                $tiersOut[] = [
                    'participants' => $n,
                    'off_each' => $off,
                    'label' => $label !== '' ? $this->translateSegment($label, $source, $target) : '',
                ];
            }
        }
        $out = [
            'base_price_text' => $base !== '' ? $this->translateSegment($base, $source, $target) : '',
            'intro_text' => $intro !== '' ? $this->translatePlain($intro, $source, $target) : '',
            'tiers' => $tiersOut,
        ];
        $enc = json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $enc !== false ? $enc : $json;
    }

    /**
     * Translate the default promotion tier sentence while keeping #N# and #OFF# for runtime substitution.
     */
    private function translatePromotionTierOfferTemplate(string $text, string $source, string $target): string
    {
        if (trim($text) === '') {
            return '';
        }
        if ($source === $target) {
            return $text;
        }
        $markN = "\u{E000}";
        $markOff = "\u{E001}";
        $masked = str_replace(['#N#', '#OFF#'], [$markN, $markOff], $text);
        $out = $this->translatePlain($masked, $source, $target);

        return str_replace([$markN, $markOff], ['#N#', '#OFF#'], $out);
    }

    /**
     * Translate value|label|icon — icon key (3rd column) is never translated.
     */
    private function translateHeroStatsPipeLines(string $text, string $source, string $target): string
    {
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $line = rtrim((string) $line);
            if ($line === '') {
                $out[] = '';

                continue;
            }
            $parts = explode('|', $line);
            $value = trim((string) ($parts[0] ?? ''));
            $label = trim((string) ($parts[1] ?? ''));
            $icon = trim((string) ($parts[2] ?? ''));
            $tv = $this->translateSegment($value, $source, $target);
            $tl = $this->translateSegment($label, $source, $target);
            $out[] = $icon !== '' ? $tv.'|'.$tl.'|'.$icon : $tv.'|'.$tl;
        }

        return implode("\n", $out);
    }

    /**
     * Translate text|icon lines — icon key after first pipe is preserved.
     */
    private function translatePackageItemsLines(string $text, string $source, string $target): string
    {
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                $out[] = '';

                continue;
            }
            if (! str_contains($line, '|')) {
                $out[] = $this->translateSegment($line, $source, $target);

                continue;
            }
            [$textPart, $icon] = explode('|', $line, 2);
            $tt = $this->translateSegment(trim($textPart), $source, $target);
            $ic = trim(strip_tags((string) $icon));
            $out[] = $ic !== '' ? $tt.'|'.$ic : $tt;
        }

        return implode("\n", $out);
    }

    /**
     * path|caption|headline|title — path is never translated; text columns translated when present.
     */
    private function translateTripActivityGallerySlides(string $text, string $source, string $target): string
    {
        $rows = \App\Models\LandingPage::parseTripActivityGallerySlideRows($text);
        $out = [];
        foreach ($rows as $row) {
            $cap = trim((string) ($row['caption'] ?? ''));
            $headline = trim((string) ($row['headline'] ?? ''));
            $title = trim((string) ($row['title'] ?? ''));
            $out[] = \App\Models\LandingPage::encodeTripActivityGallerySlideRow([
                'path' => $row['path'],
                'caption' => $cap !== '' ? $this->translateSegment($cap, $source, $target) : '',
                'headline' => $headline !== '' ? $this->translateSegment($headline, $source, $target) : '',
                'title' => $title !== '' ? $this->translateSegment($title, $source, $target) : '',
            ]);
        }

        return implode("\n", array_filter($out, static fn (string $l) => $l !== ''));
    }

    private function translatePipeDelimitedMultiline(string $text, string $source, string $target): string
    {
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $out[] = $this->translatePipeLine((string) $line, $source, $target);
        }

        return implode("\n", $out);
    }

    private function translatePipeLine(string $line, string $source, string $target): string
    {
        $line = rtrim($line);
        if ($line === '') {
            return '';
        }
        if (! str_contains($line, '|')) {
            return $this->translateSegment($line, $source, $target);
        }
        $parts = explode('|', $line);
        $translated = [];
        foreach ($parts as $part) {
            $translated[] = $this->translateSegment(trim($part), $source, $target);
        }

        return implode('|', $translated);
    }

    private function translateLineByLinePlain(string $text, string $source, string $target): string
    {
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $out[] = $this->translateSegment((string) $line, $source, $target);
        }

        return implode("\n", $out);
    }

    private function translateContactPhones(string $text, string $source, string $target): string
    {
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $trim = trim((string) $line);
            if ($trim === '') {
                $out[] = '';

                continue;
            }
            if (preg_match('/^[\d\s\+\-\(\)]+$/u', $trim)) {
                $out[] = $line;

                continue;
            }
            $out[] = $this->translateSegment((string) $line, $source, $target);
        }

        return implode("\n", $out);
    }

    private function translatePlain(string $text, string $source, string $target): string
    {
        if (mb_strlen($text) <= self::MAX_CHUNK_CHARS) {
            return $this->translateSegment($text, $source, $target);
        }
        $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            if ($line === '') {
                $out[] = '';

                continue;
            }
            $out[] = $this->translateLongLine((string) $line, $source, $target);
        }

        return implode("\n", $out);
    }

    private function translateLongLine(string $line, string $source, string $target): string
    {
        if (mb_strlen($line) <= self::MAX_CHUNK_CHARS) {
            return $this->translateSegment($line, $source, $target);
        }
        $chunks = [];
        $len = mb_strlen($line);
        $size = self::MAX_CHUNK_CHARS;
        for ($i = 0; $i < $len; $i += $size) {
            $chunks[] = mb_substr($line, $i, $size);
        }
        $translated = [];
        foreach ($chunks as $chunk) {
            $translated[] = $this->translateSegment($chunk, $source, $target);
        }

        return implode('', $translated);
    }

    private function translateSegment(string $text, string $source, string $target): string
    {
        if (trim($text) === '') {
            return $text;
        }

        return $this->callMyMemory($text, $source, $target);
    }

    private function callMyMemory(string $text, string $source, string $target): string
    {
        $pair = $this->langPair($source, $target);
        $response = Http::timeout(25)->acceptJson()->get(self::MYMEMORY_URL, [
            'q' => $text,
            'langpair' => $pair,
        ]);

        if (! $response->successful()) {
            Log::warning('Landing MyMemory HTTP error', ['status' => $response->status()]);

            throw new \RuntimeException('Translation service HTTP error.');
        }

        $json = $response->json();
        $status = (int) ($json['responseStatus'] ?? 0);
        if ($status !== 200) {
            $rd = $json['responseData'] ?? null;
            if (is_string($rd)) {
                $msg = $rd;
            } elseif (is_array($rd)) {
                $msg = (string) ($rd['error'] ?? 'Translation failed');
            } else {
                $msg = 'Translation failed';
            }
            Log::warning('Landing MyMemory API error', ['response' => $json]);

            throw new \RuntimeException($msg);
        }

        return (string) ($json['responseData']['translatedText'] ?? '');
    }

    private function langPair(string $source, string $target): string
    {
        return $this->mapLocale($source).'|'.$this->mapLocale($target);
    }

    private function mapLocale(string $locale): string
    {
        return match ($locale) {
            'zh' => 'zh-CN',
            default => $locale,
        };
    }
}
