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
            'package_price', 'booking_title', 'faq_title',
            'trip_section_title', 'per_person_label', 'seats_left_suffix',
            'hero_stats_text', 'package_items_text', 'trip_dates_text', 'faq_items_text', 'contact_phones_text',
            'booking_name_placeholder', 'booking_email_placeholder', 'booking_phone_placeholder',
            'booking_trip_placeholder', 'booking_submit_text',
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
            'hero_stats_text', 'trip_dates_text', 'faq_items_text' => $this->translatePipeDelimitedMultiline($text, $sourceLocale, $targetLocale),
            'package_items_text' => $this->translateLineByLinePlain($text, $sourceLocale, $targetLocale),
            'contact_phones_text' => $this->translateContactPhones($text, $sourceLocale, $targetLocale),
            default => $this->translatePlain($text, $sourceLocale, $targetLocale),
        };
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
