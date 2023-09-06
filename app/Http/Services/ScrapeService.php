<?php

namespace App\Http\Services;

use App\Models\Movie;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeService
{
    /**
     * Default constructor to load data and view
     * @param Movie $movie
     */
    public function __construct(
        protected Movie $movie
    ) {
    }

    public function getAllMovies()
    {
        return $this->movie->latest()->paginate(5);
    }

    public function scrape($url = 'https://www.imdb.com/chart/top')
    {
        $client = new Client(HttpClient::create(['timeout' => 60]));
        try {
            $crawler = $client->request('GET', $url);
        } catch (\Exception $e) {
            Log::error("{$e->getCode()} - {$e->getMessage()}");

            return;
        }
        $crawler->filter('.ipc-metadata-list .ipc-metadata-list-summary-item')
        ->slice(0, 10)
        ->each(function ($item) {
        $title = preg_replace('/^\d+\.\s*/', '', $item->filter('.ipc-title__text')->text());
        $year = (int) $item->filter('.cli-title-metadata-item')->text();
        $rating = (float) $item->filter('.ipc-rating-star--imdb')->text();
        $url =  sprintf("https://www.imdb.com/%s", $item->filter('.ipc-title-link-wrapper')->attr('href'));
            try {
                $this->movie->updateOrCreate(['title' => $title], [
                    'url' => $url,
                    'year' => $year,
                    'title' => $title,
                    'rating' => $rating,
                ]);
            } catch (\Exception $e) {
                Log::error("Error inserting movie: {$title} - {$e->getMessage()}");
            }
        });
    }
}
