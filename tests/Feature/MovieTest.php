<?php

namespace Tests\Feature;

use App\Jobs\ScrapeJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MovieTest extends TestCase
{
    public function testScrapingJob()
    {
        Queue::fake();
        dispatch(new ScrapeJob());
        Queue::assertPushed(ScrapeJob::class);
    }

    public function testMovies()
    {
        $response = $this->get(route('movies'))->assertOk();
    }
}
