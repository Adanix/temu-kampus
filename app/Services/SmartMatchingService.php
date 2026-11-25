<?php

namespace App\Services;

use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Enums\NotificationEventType;
use App\Models\ItemMatch;
use App\Models\ItemReport;
use App\Services\Notifications\NotificationDispatcherInterface;
use Illuminate\Support\Collection;

class SmartMatchingService
{
    /**
     * @var array<int, string>
     */
    private const array STOPWORDS = [
        'yang', 'dan', 'di', 'ke', 'dari', 'pada', 'dengan', 'untuk', 'atau', 'ini', 'itu',
        'ada', 'oleh', 'saat', 'dekat', 'dalam', 'warna', 'barang', 'hilang', 'ditemukan',
        'milik', 'berisi', 'sekitar', 'gedung', 'lantai', 'ruang', 'kampus', 'saya', 'sudah',
        'the', 'and', 'for', 'with', 'from', 'that', 'this', 'near', 'inside', 'found',
        'lost', 'item', 'color', 'building', 'floor', 'room', 'campus', 'was', 'has', 'have',
    ];

    public function __construct(
        private readonly NotificationDispatcherInterface $notifier,
    ) {}

    /**
     * Compute deterministic weighted match score (0-100) between a lost item and found item.
     *
     * @return array{score: int, reasons: array{category: int, zone: int, keywords: int, date: int, matched_tokens: array<int, string>}}
     */
    public function calculateMatch(ItemReport $lost, ItemReport $found): array
    {
        if ($lost->id !== null && $lost->id === $found->id) {
            return [
                'score' => 0,
                'reasons' => [
                    'category' => 0,
                    'zone' => 0,
                    'keywords' => 0,
                    'date' => 0,
                    'matched_tokens' => [],
                ],
            ];
        }

        if ($lost->category !== $found->category) {
            return [
                'score' => 0,
                'reasons' => [
                    'category' => 0,
                    'zone' => 0,
                    'keywords' => 0,
                    'date' => 0,
                    'matched_tokens' => [],
                ],
            ];
        }

        $categoryScore = 35;

        $lost->loadMissing('campusZone');
        $found->loadMissing('campusZone');

        if ($lost->campus_zone_id === $found->campus_zone_id) {
            $zoneScore = 25;
        } elseif ($lost->campusZone?->zone_group !== null && $lost->campusZone->zone_group === $found->campusZone?->zone_group) {
            $zoneScore = 12;
        } else {
            $zoneScore = 0;
        }

        $tokensLost = $this->tokenize($lost);
        $tokensFound = $this->tokenize($found);
        $common = array_values(array_intersect($tokensLost, $tokensFound));

        if (count($tokensLost) === 0 || count($tokensFound) === 0) {
            $keywordScore = 0;
        } else {
            $denom = max(1, min(count($tokensLost), count($tokensFound)));
            $keywordScore = min(25, (int) round((count($common) / $denom) * 25));
        }

        $daysDiff = (int) abs($lost->incident_date->startOfDay()->diffInDays($found->incident_date->startOfDay(), false));
        $dateScore = match (true) {
            $daysDiff <= 2 => 15,
            $daysDiff <= 7 => 10,
            $daysDiff <= 14 => 5,
            default => 0,
        };

        $total = $categoryScore + $zoneScore + $keywordScore + $dateScore;

        return [
            'score' => $total,
            'reasons' => [
                'category' => $categoryScore,
                'zone' => $zoneScore,
                'keywords' => $keywordScore,
                'date' => $dateScore,
                'matched_tokens' => array_slice($common, 0, 8),
            ],
        ];
    }

    /**
     * Generate and persist smart matches (score >= 60) for a given open item report.
     *
     * @return Collection<int, ItemMatch>
     */
    public function generateMatchesForItem(ItemReport $item, bool $notify = true): Collection
    {
        if ($item->status !== ItemStatus::Open) {
            return collect();
        }

        $oppositeType = $item->type === ItemType::Lost ? ItemType::Found : ItemType::Lost;

        $candidates = ItemReport::with(['campusZone', 'reporter'])
            ->where('type', $oppositeType->value)
            ->where('status', ItemStatus::Open->value)
            ->where('category', $item->category->value)
            ->where('id', '!=', $item->id)
            ->get();

        $item->loadMissing(['campusZone', 'reporter']);

        $validPairKeys = [];
        $persisted = collect();

        foreach ($candidates as $candidate) {
            $lost = $item->type === ItemType::Lost ? $item : $candidate;
            $found = $item->type === ItemType::Found ? $item : $candidate;

            $result = $this->calculateMatch($lost, $found);

            if ($result['score'] < 60) {
                continue;
            }

            $validPairKeys[] = $lost->id.':'.$found->id;

            $existing = ItemMatch::where('lost_item_id', $lost->id)
                ->where('found_item_id', $found->id)
                ->first();

            if ($existing !== null) {
                $existing->update([
                    'score' => $result['score'],
                    'match_reasons' => $result['reasons'],
                ]);
                $persisted->push($existing);

                continue;
            }

            $match = ItemMatch::create([
                'lost_item_id' => $lost->id,
                'found_item_id' => $found->id,
                'score' => $result['score'],
                'match_reasons' => $result['reasons'],
                'is_dismissed' => false,
            ]);

            $persisted->push($match);

            if ($notify) {
                $this->notifyMatch($lost, $found, $result['score']);
            }
        }

        // Remove stale matches for this item if edits reduced score below 60 or changed category
        $relationColumn = $item->type === ItemType::Lost ? 'lost_item_id' : 'found_item_id';
        ItemMatch::where($relationColumn, $item->id)
            ->get()
            ->each(function (ItemMatch $existingMatch) use ($validPairKeys): void {
                $key = $existingMatch->lost_item_id.':'.$existingMatch->found_item_id;
                if (! in_array($key, $validPairKeys, true)) {
                    $existingMatch->delete();
                }
            });

        return $persisted;
    }

    /**
     * @return array<int, string>
     */
    private function tokenize(ItemReport $report): array
    {
        $raw = implode(' ', array_filter([
            $report->title,
            $report->description,
            $report->color,
            $report->brand,
        ]));

        $normalized = mb_strtolower($raw);
        $cleaned = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $normalized) ?? '';
        $parts = preg_split('/\s+/', trim($cleaned)) ?: [];

        $tokens = array_filter($parts, function (string $token): bool {
            return mb_strlen($token) >= 2 && ! in_array($token, self::STOPWORDS, true);
        });

        return array_values(array_unique($tokens));
    }

    private function notifyMatch(ItemReport $lost, ItemReport $found, int $score): void
    {
        if ($lost->reporter !== null) {
            $this->notifier->dispatch(
                recipient: $lost->reporter,
                eventType: NotificationEventType::MatchFound,
                subjectId: sprintf('Kecocokan %d%% ditemukan untuk %s', $score, $lost->ticket_code),
                subjectEn: sprintf('%d%% match found for %s', $score, $lost->ticket_code),
                bodyId: sprintf(
                    'Sistem mendeteksi laporan temuan "%s" (%s) dengan skor kecocokan %d%% terhadap laporan kehilangan "%s" (%s). Silakan tinjau detailnya dan ajukan klaim jika sesuai.',
                    $found->title,
                    $found->ticket_code,
                    $score,
                    $lost->title,
                    $lost->ticket_code,
                ),
                bodyEn: sprintf(
                    'The matching engine detected a %d%% match between found item "%s" (%s) and your lost report "%s" (%s). Review the details and submit a claim if it belongs to you.',
                    $score,
                    $found->title,
                    $found->ticket_code,
                    $lost->title,
                    $lost->ticket_code,
                ),
                relatedItemId: $found->id,
            );
        }

        if ($found->reporter !== null && $found->reporter_id !== $lost->reporter_id) {
            $this->notifier->dispatch(
                recipient: $found->reporter,
                eventType: NotificationEventType::MatchFound,
                subjectId: sprintf('Laporan kehilangan cocok (%d%%) dengan temuan %s', $score, $found->ticket_code),
                subjectEn: sprintf('Lost report matches (%d%%) your found item %s', $score, $found->ticket_code),
                bodyId: sprintf(
                    'Laporan kehilangan "%s" (%s) cocok %d%% dengan barang temuan "%s" (%s) yang Anda laporkan.',
                    $lost->title,
                    $lost->ticket_code,
                    $score,
                    $found->title,
                    $found->ticket_code,
                ),
                bodyEn: sprintf(
                    'Lost report "%s" (%s) matches %d%% with the found item "%s" (%s) you reported.',
                    $lost->title,
                    $lost->ticket_code,
                    $score,
                    $found->title,
                    $found->ticket_code,
                ),
                relatedItemId: $found->id,
            );
        }
    }
}
