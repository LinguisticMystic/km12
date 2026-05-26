<?php

namespace App\Wayfinder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class WayfinderController extends Controller
{
    public function __invoke(): View
    {
        $base = asset(config('wayfinder.assets_path'));

        $floors = collect(config('wayfinder.floors', []))
            ->map(fn (array $floor) => [
                'id' => $floor['id'],
                'label' => $floor['label'],
                'json' => $base.'/'.($floor['json'] ?? ''),
                'image' => $base.'/'.($floor['image'] ?? ''),
            ])
            ->values()
            ->all();

        return view('wayfinder::show', [
            'mapAssets' => [
                'floors' => $floors,
            ],
            'scriptUrl' => $base.'/wayfinder.js',
        ]);
    }
}
