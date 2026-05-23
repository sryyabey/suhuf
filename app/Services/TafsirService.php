<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TafsirService
{
    private const QURAN_API   = 'https://api.quran.com/api/v4';
    private const ALQURAN_API = 'https://api.alquran.cloud/v1';
    private const NULL_VAL    = '__null__';

    /**
     * Türkçe tefsirler — ID >= 10001, alquran.cloud üzerinden çekilir.
     * Anahtar: bizim dahili ID, Değer: [identifier (alquran.cloud), name, author_name]
     */
    private const TURKISH_TAFSIRS = [
        10001 => ['identifier' => 'tr.yazir',    'name' => 'Hak Dini Kuran Dili',       'author_name' => 'Elmalılı Hamdi Yazır'],
        10002 => ['identifier' => 'tr.diyanet',  'name' => 'Diyanet İşleri Meali',      'author_name' => 'Diyanet İşleri Başkanlığı'],
        10003 => ['identifier' => 'tr.vakfi',    'name' => 'Diyanet Vakfı Meali',       'author_name' => 'Diyanet Vakfı'],
        10004 => ['identifier' => 'tr.ates',     'name' => 'Yüce Kuran\'ın Çağdaş Tefsiri', 'author_name' => 'Süleyman Ateş'],
        10005 => ['identifier' => 'tr.bulac',    'name' => 'Kuran-ı Kerim ve Türkçe Anlamı', 'author_name' => 'Ali Bulaç'],
        10006 => ['identifier' => 'tr.yildirim', 'name' => 'Kuran-ı Hakim ve Meali',    'author_name' => 'Suat Yıldırım'],
        10007 => ['identifier' => 'tr.ozturk',   'name' => 'Kuran-ı Kerim ve Türkçe Meali', 'author_name' => 'Yaşar Nuri Öztürk'],
        10008 => ['identifier' => 'tr.golpinarli','name' => 'Kuran Kerim ve Meali',      'author_name' => 'Abdülbaki Gölpınarlı'],
    ];

    /* ── Mevcut tefsir listesi ────────────────────────────────────── */

    /**
     * Tüm tefsirleri dile göre grupla.
     * Türkçe girişler başa eklenir, Quran.com listesi 7 gün cache'lenir.
     *
     * @return array<string, list<array{id:int, name:string, author_name:string, language_name:string}>>
     */
    public function getGroupedByLanguage(): array
    {
        $remote = Cache::remember('tafsir.list.all', now()->addDays(7), function () {
            try {
                $response = Http::timeout(10)
                    ->acceptJson()
                    ->get(self::QURAN_API . '/resources/tafsirs');

                if ($response->successful()) {
                    return $response->json('tafsirs', []);
                }
            } catch (\Throwable $e) {
                Log::warning('TafsirService: liste alınamadı', ['err' => $e->getMessage()]);
            }

            return [];
        });

        // Türkçe girişleri oluştur
        $turkish = array_map(
            fn (int $id, array $t) => [
                'id'            => $id,
                'name'          => $t['name'],
                'author_name'   => $t['author_name'],
                'language_name' => 'turkish',
            ],
            array_keys(self::TURKISH_TAFSIRS),
            self::TURKISH_TAFSIRS
        );

        // Quran.com listesini grupla ve Türkçe'yi başa ekle
        $remoteGrouped = collect($remote)
            ->groupBy('language_name')
            ->map(fn ($g) => $g->sortBy('name')->values()->all())
            ->toArray();

        return array_merge(['turkish' => $turkish], $remoteGrouped);
    }

    /* ── Ayet tefsiri / meali ─────────────────────────────────────── */

    /**
     * Belirtilen ayet için tefsir metnini döndür.
     * ID >= 10001 → alquran.cloud (Türkçe)
     * ID < 10001  → Quran.com API
     * Başarılı sonuçlar 30 gün, başarısız sonuçlar 1 saat cache'lenir.
     */
    public function getAyahTafsir(int $sura, int $aya, int $tafsirId): ?string
    {
        $key    = "tafsir.ayah.{$tafsirId}.{$sura}.{$aya}";
        $cached = Cache::get($key);

        if ($cached !== null) {
            return $cached === self::NULL_VAL ? null : $cached;
        }

        $text = $tafsirId >= 10001
            ? $this->fetchTurkishAyah($sura, $aya, $tafsirId)
            : $this->fetchQuranComAyah($sura, $aya, $tafsirId);

        if ($text !== null) {
            Cache::put($key, $text, now()->addDays(30));
        } else {
            Cache::put($key, self::NULL_VAL, now()->addHour());
        }

        return $text;
    }

    /* ── Private: Quran.com ───────────────────────────────────────── */

    private function fetchQuranComAyah(int $sura, int $aya, int $tafsirId): ?string
    {
        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->get(self::QURAN_API . "/tafsirs/{$tafsirId}/by_ayah/{$sura}:{$aya}");

            if ($response->successful()) {
                $raw = $response->json('tafsir.text', '');

                if ($raw) {
                    return html_entity_decode(
                        strip_tags((string) $raw),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    ) ?: null;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TafsirService: Quran.com ayet tefsiri alınamadı', [
                'tafsir_id' => $tafsirId,
                'sura'      => $sura,
                'aya'       => $aya,
                'err'       => $e->getMessage(),
            ]);
        }

        return null;
    }

    /* ── Private: alquran.cloud (Türkçe) ─────────────────────────── */

    private function fetchTurkishAyah(int $sura, int $aya, int $tafsirId): ?string
    {
        $meta = self::TURKISH_TAFSIRS[$tafsirId] ?? null;
        if (! $meta) {
            return null;
        }

        try {
            $identifier = $meta['identifier'];
            $response   = Http::timeout(8)
                ->acceptJson()
                ->get(self::ALQURAN_API . "/ayah/{$sura}:{$aya}/{$identifier}");

            if ($response->successful()) {
                $text = $response->json('data.text', '');

                if ($text) {
                    return html_entity_decode(
                        strip_tags((string) $text),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    ) ?: null;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TafsirService: alquran.cloud Türkçe meali alınamadı', [
                'tafsir_id'  => $tafsirId,
                'identifier' => $meta['identifier'],
                'sura'       => $sura,
                'aya'        => $aya,
                'err'        => $e->getMessage(),
            ]);
        }

        return null;
    }
}
