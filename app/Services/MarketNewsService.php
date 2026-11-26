<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarketNewsService
{
    protected string $baseUrl;

    protected array $topicQueries = [
        'global_market' => 'global market economy trade',
        'maritime_shipping' => 'maritime shipping container freight',
        'supply_chain' => 'global supply chain logistics port',
        'indonesia_trade' => 'indonesia export import port trade',
    ];

    public function __construct()
    {
        $this->baseUrl = config('cargotrace.market_news_api_url', 'https://news.google.com/rss/search');
    }

    /**
     * Fetch global market & logistics news from Google News RSS (free, no API key).
     * Caches for 15 minutes with resilient fallback on network/upstream failure.
     */
    public function getNews(string $topic = 'global_market', int $limit = 12): array
    {
        $topic = array_key_exists($topic, $this->topicQueries) ? $topic : 'global_market';
        $query = $this->topicQueries[$topic];
        $cacheKey = "market_news_{$topic}_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($query, $limit, $topic) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'CargoTrace/1.0 (Logistics Platform)',
                ])
                ->timeout(6)
                ->retry(2, 200)
                ->get($this->baseUrl, [
                    'q' => $query,
                    'hl' => 'en-US',
                    'gl' => 'US',
                    'ceid' => 'US:en',
                ]);

                if ($response->successful() && !empty($response->body())) {
                    $articles = $this->parseRss($response->body(), $limit);
                    if (!empty($articles)) {
                        return [
                            'source_provider' => 'Google News RSS (Live)',
                            'topic' => $topic,
                            'is_fallback' => false,
                            'articles' => $articles,
                        ];
                    }
                }
            } catch (Exception $e) {
                Log::warning("MarketNewsService failed for topic [{$topic}]: " . $e->getMessage());
            }

            return [
                'source_provider' => 'CargoTrace Offline Digest (Fallback)',
                'topic' => $topic,
                'is_fallback' => true,
                'articles' => $this->getFallbackArticles(),
            ];
        });
    }

    /**
     * Parse RSS XML response into clean array items.
     */
    protected function parseRss(string $xmlBody, int $limit): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlBody);
        libxml_clear_errors();

        if (!$xml || !isset($xml->channel->item)) {
            return [];
        }

        $articles = [];
        foreach ($xml->channel->item as $item) {
            if (count($articles) >= $limit) {
                break;
            }

            $rawTitle = (string) $item->title;
            $source = isset($item->source) ? (string) $item->source : 'Global Wire';
            $cleanTitle = preg_replace('/\s+-\s+' . preg_quote($source, '/') . '$/', '', $rawTitle);

            $articles[] = [
                'title' => $cleanTitle ?: $rawTitle,
                'url' => (string) $item->link,
                'source' => $source,
                'published_at' => Carbon::parse((string) $item->pubDate)->toIso8601String(),
            ];
        }

        return $articles;
    }

    protected function getFallbackArticles(): array
    {
        return [
            [
                'title' => 'Global Container Freight Rates Stabilize Across Major Asia-Europe Maritime Corridors',
                'url' => 'https://news.google.com',
                'source' => 'CargoTrace Digest',
                'published_at' => Carbon::now()->subHours(2)->toIso8601String(),
            ],
            [
                'title' => 'Southeast Asian Port Authorities Expand Digital Customs & Berthing Capacity',
                'url' => 'https://news.google.com',
                'source' => 'CargoTrace Digest',
                'published_at' => Carbon::now()->subHours(5)->toIso8601String(),
            ],
        ];
    }
}
