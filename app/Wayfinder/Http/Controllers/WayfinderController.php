<?php

namespace App\Wayfinder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class WayfinderController extends Controller
{
    public function __invoke(): View
    {
        $base = asset(config('wayfinder.assets_path'));

        return view('wayfinder::show', [
            'mapAssets' => [
                'json' => $base.'/floor-plan.json',
                'png' => $base.'/'.config('wayfinder.floor_plan_image'),
                'dataJs' => $base.'/floor-plan.data.js',
            ],
            'scriptUrl' => $base.'/wayfinder.js',
        ]);
    }
}
