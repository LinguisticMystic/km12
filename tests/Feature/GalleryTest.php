<?php

namespace Tests\Feature;

use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_galleries_index_is_successful(): void
    {
        $response = $this->get(route('galleries.index'));

        $response->assertOk();
        $response->assertSee('Galerijas');
        $response->assertSee('Vēl nav galeriju');
    }

    public function test_galleries_index_lists_galleries_with_images(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Saknes un asni 2026',
        ]);

        $gallery->images()->create([
            'path' => 'galleries/1/cover.jpg',
            'annotation' => 'Photo: Anna',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('galleries.index'));

        $response->assertOk();
        $response->assertSee('Saknes un asni 2026');
        $response->assertSee('/galleries/saknes-un-asni-2026', false);
        $response->assertSee(route('galleries.show', $gallery), false);
        $response->assertSee('1 foto');
        $response->assertDontSee('Vēl nav galeriju');
    }

    public function test_empty_galleries_are_hidden_from_the_index(): void
    {
        Gallery::query()->create([
            'name' => 'Empty Hall',
        ]);

        $this->get(route('galleries.index'))
            ->assertOk()
            ->assertDontSee('Empty Hall')
            ->assertSee('Vēl nav galeriju');
    }

    public function test_galleries_show_is_successful(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Workshop Night',
        ]);

        $gallery->images()->create([
            'path' => 'galleries/1/one.jpg',
            'annotation' => 'Foto: Jānis Bērziņš',
            'annotation_en' => 'Photo: Jānis Bērziņš',
            'sort_order' => 1,
        ]);

        $gallery->images()->create([
            'path' => 'galleries/1/two.jpg',
            'annotation' => null,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('galleries.show', $gallery));

        $response->assertOk();
        $response->assertSee('Workshop Night');
        $response->assertSee('data-caption="Foto: Jānis Bērziņš"', false);
        $response->assertDontSee('<figcaption', false);
        $response->assertSee('data-gallery-prev', false);
        $response->assertSee('data-gallery-next', false);
        $response->assertSee('/storage/galleries/1/one.jpg', false);
        $response->assertSee('/storage/galleries/1/two.jpg', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
    }

    public function test_galleries_show_uses_a_slug_from_the_gallery_name(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Saknes un asni 2026',
        ]);

        $this->assertSame('saknes-un-asni-2026', $gallery->slug);
        $this->assertStringEndsWith('/galleries/saknes-un-asni-2026', route('galleries.show', $gallery));

        $this->get(route('galleries.show', $gallery))
            ->assertOk()
            ->assertSee('Saknes un asni 2026');
    }

    public function test_numeric_gallery_urls_redirect_to_the_slug(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Saknes un asni 2026',
        ]);

        $this->get('/galleries/'.$gallery->id)
            ->assertRedirectToRoute('galleries.show', $gallery)
            ->assertStatus(301);
    }

    public function test_duplicate_gallery_names_get_unique_slugs(): void
    {
        $first = Gallery::query()->create([
            'name' => 'Community Night',
        ]);

        $second = Gallery::query()->create([
            'name' => 'Community Night',
        ]);

        $this->assertSame('community-night', $first->slug);
        $this->assertSame('community-night-2', $second->slug);

        $this->get('/galleries/community-night')->assertOk()->assertSee('Community Night');
        $this->get('/galleries/community-night-2')->assertOk()->assertSee('Community Night');
    }

    public function test_gallery_slug_updates_when_the_name_changes(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Old Name',
        ]);

        $this->assertSame('old-name', $gallery->slug);

        $gallery->update(['name' => 'Jāņa Čakste 2026']);

        $this->assertSame('jana-cakste-2026', $gallery->fresh()->slug);
    }

    public function test_numeric_gallery_names_do_not_clash_with_id_urls(): void
    {
        $gallery = Gallery::query()->create([
            'name' => '2026',
        ]);

        $this->assertSame('gallery-2026', $gallery->slug);

        $this->get('/galleries/'.$gallery->id)
            ->assertRedirectToRoute('galleries.show', $gallery)
            ->assertStatus(301);

        $this->get('/galleries/gallery-2026')->assertOk()->assertSee('2026');
    }

    public function test_home_page_links_to_galleries(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('galleries.index'), false);
        $response->assertSee('Galerijas');
    }

    public function test_deleting_an_image_removes_its_file(): void
    {
        Storage::fake('public');

        $gallery = Gallery::query()->create([
            'name' => 'Archive',
        ]);

        Storage::disk('public')->put('galleries/1/photo.jpg', 'fake-image');

        $image = $gallery->images()->create([
            'path' => 'galleries/1/photo.jpg',
            'annotation' => null,
            'sort_order' => 1,
        ]);

        $image->delete();

        Storage::disk('public')->assertMissing('galleries/1/photo.jpg');
        $this->assertDatabaseMissing('gallery_images', ['id' => $image->id]);
    }

    public function test_deleting_a_gallery_removes_its_images_and_files(): void
    {
        Storage::fake('public');

        $gallery = Gallery::query()->create([
            'name' => 'Archive',
        ]);

        Storage::disk('public')->put('galleries/1/photo.jpg', 'fake-image');

        $image = $gallery->images()->create([
            'path' => 'galleries/1/photo.jpg',
            'annotation' => 'Photo: Anna',
            'sort_order' => 1,
        ]);

        $gallery->delete();

        Storage::disk('public')->assertMissing('galleries/1/photo.jpg');
        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
        $this->assertDatabaseMissing('gallery_images', ['id' => $image->id]);
    }

    public function test_gallery_annotations_follow_the_active_locale(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Workshop Night',
        ]);

        $gallery->images()->create([
            'path' => 'galleries/1/one.jpg',
            'annotation' => 'Foto: Jānis Bērziņš',
            'annotation_en' => 'Photo: John Birch',
            'sort_order' => 1,
        ]);

        $this->get(route('galleries.show', $gallery))
            ->assertOk()
            ->assertSee('data-caption="Foto: Jānis Bērziņš"', false)
            ->assertDontSee('data-caption="Photo: John Birch"', false);

        $this->from(route('galleries.show', $gallery))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('galleries.show', $gallery));

        $this->get(route('galleries.show', $gallery))
            ->assertOk()
            ->assertSee('data-caption="Photo: John Birch"', false)
            ->assertDontSee('data-caption="Foto: Jānis Bērziņš"', false);
    }

    public function test_gallery_annotations_fall_back_when_a_translation_is_missing(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Workshop Night',
        ]);

        $gallery->images()->create([
            'path' => 'galleries/1/one.jpg',
            'annotation' => 'Foto: Anna',
            'annotation_en' => null,
            'sort_order' => 1,
        ]);

        $this->from(route('galleries.show', $gallery))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('galleries.show', $gallery));

        $this->get(route('galleries.show', $gallery))
            ->assertOk()
            ->assertSee('data-caption="Foto: Anna"', false);
    }
}
