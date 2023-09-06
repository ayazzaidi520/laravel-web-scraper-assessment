<?php

namespace App\Http\Controllers;

use App\Http\Services\ScrapeService;

class MovieController extends Controller
{
    /**
     * Default constructor to load data and view
     * @param ScrapeService $scrapeService
     */
    public function __construct(
        protected ScrapeService $scrapeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('movies.index', ['movies' => $this->scrapeService->getAllMovies()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $this->scrapeService->scrape('https://www.imdb.com/chart/top/');

        return redirect()->route('movies')->with('success','Movies has been created successfully.');
    }
}
