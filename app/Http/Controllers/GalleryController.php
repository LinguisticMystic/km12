<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::query()
            ->whereHas('images')
            ->with('coverImage')
            ->withCount('images')
            ->latest()
            ->get();

        return view('galleries.index', compact('galleries'));
    }

    public function redirectFromId(int $id): RedirectResponse
    {
        $gallery = Gallery::query()->findOrFail($id);

        return redirect()->route('galleries.show', $gallery, 301);
    }

    public function show(Gallery $gallery): View
    {
        $gallery->load('images');

        return view('galleries.show', compact('gallery'));
    }
}
