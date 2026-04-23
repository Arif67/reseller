<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(private readonly CampaignService $campaignService)
    {
    }

    public function show(string $slug, Request $request)
    {
        return view('frontEnd.pages.campaign.campaign', $this->campaignService->getCampaignPageData($slug, $request));
    }
}
