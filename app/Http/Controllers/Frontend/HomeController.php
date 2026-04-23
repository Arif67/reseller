<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\HomePageService;

class HomeController extends Controller
{
    public function __construct(private readonly HomePageService $homePageService)
    {
    }

    public function index()
    {
        return view('frontEnd.pages.index', $this->homePageService->getHomePageData());
    }

    public function storepage()
    {
        return view('frontEnd.pages.store', $this->homePageService->getStorePageData());
    }

    public function offers()
    {
        return view('frontEnd.pages.offers');
    }
}
