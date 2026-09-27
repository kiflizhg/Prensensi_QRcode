<?php

namespace Tests\Feature;

use App\Models\PublicPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_replace_guide_illustration_and_restore_default(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $data = array_replace(PublicPage::defaults(), ['guide_caption' => 'Panduan presensi sekolah']);
        $this->get(route('home'))->assertSee('guide-attendance-3d.png');
        $this->put(route('admin.public-page.update'), $data + ['guide_photo' => UploadedFile::fake()->image('panduan.png')])->assertSessionHasNoErrors();
        $path = PublicPage::content()->guide_photo_path;
        Storage::disk('public')->assertExists($path);
        $this->get(route('home'))->assertSee(asset('storage/'.$path))->assertSee('Panduan presensi sekolah');
        $this->put(route('admin.public-page.update'), $data)->assertSessionHasNoErrors();
        $this->assertSame($path, PublicPage::content()->guide_photo_path);
        $this->put(route('admin.public-page.update'), $data + ['remove_guide_photo' => '1'])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($path);
        $this->get(route('home'))->assertSee('guide-attendance-3d.png');
    }

    public function test_content_photo_caption_and_footer_can_be_updated(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.public-page.edit'))->assertOk()->assertSee('3. Konten dan Foto Sekolah');
        $data = array_replace(PublicPage::defaults(), ['content_caption' => 'Kegiatan sekolah', 'principal_caption' => 'Kepala sekolah kami']);
        $this->put(route('admin.public-page.update'), $data + ['content_photo' => UploadedFile::fake()->image('konten.jpg')])->assertSessionHasNoErrors();
        $path = PublicPage::content()->content_photo_path;
        Storage::disk('public')->assertExists($path);
        $this->get(route('home'))->assertOk()->assertSee('Kegiatan sekolah')->assertSee('Kepala sekolah kami')->assertSee(asset('storage/'.$path))
            ->assertSeeInOrder(['</main>', 'Temukan Kami', 'Media sosial dan website sekolah'], false);
        $this->put(route('admin.public-page.update'), $data)->assertSessionHasNoErrors();
        $this->assertSame($path, PublicPage::content()->content_photo_path);
        $this->put(route('admin.public-page.update'), $data + ['content_photo' => UploadedFile::fake()->image('baru.png')])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($path);
        $newPath = PublicPage::content()->content_photo_path;
        $this->put(route('admin.public-page.update'), $data + ['content_photo' => UploadedFile::fake()->create('file.txt', 10, 'text/plain')])->assertSessionHasErrors('content_photo');
        $this->assertSame($newPath, PublicPage::content()->content_photo_path);
        $this->put(route('admin.public-page.update'), $data + ['remove_content_photo' => '1'])->assertSessionHasNoErrors();
        $this->assertNull(PublicPage::content()->content_photo_path);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_social_links_and_welcome_order_are_managed_by_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $data = array_replace(PublicPage::defaults(), [
            'principal_bio' => 'Salam pembukaan dari kepala sekolah.',
            'whatsapp_url' => 'https://wa.me/6281234567890',
            'instagram_url' => 'https://www.instagram.com/sekolah',
            'website_url' => 'https://sekolah.example.org',
        ]);
        $this->put(route('admin.public-page.update'), $data)->assertSessionHasNoErrors();
        $this->get(route('home'))->assertOk()
            ->assertSeeInOrder(['SAMBUTAN KEPALA SEKOLAH', 'Salam pembukaan', 'Visi & Misi Sekolah', 'Temukan Kami'], false)
            ->assertSee('href="'.$data['whatsapp_url'].'"', false)
            ->assertSee('href="'.$data['instagram_url'].'"', false)
            ->assertSee('href="'.$data['website_url'].'"', false);
        $this->put(route('admin.public-page.update'), array_replace($data, ['website_url' => 'javascript:alert(1)']))->assertSessionHasErrors('website_url');
        $this->assertSame($data['website_url'], PublicPage::content()->website_url);
        $this->put(route('admin.public-page.update'), array_replace($data, ['whatsapp_url' => '', 'instagram_url' => '', 'website_url' => '']))->assertSessionHasNoErrors();
        $this->get(route('home'))->assertSee('Tautan belum tersedia')->assertDontSee('href="'.$data['website_url'].'"', false);
    }

    public function test_only_admin_can_edit_public_content(): void
    {
        $this->withoutVite();
        $this->get(route('home'))->assertOk()->assertSee('Panduan')->assertSee('Kepala Sekolah');
        $this->get(route('admin.public-page.edit'))->assertRedirect(route('login'));
        $this->put(route('admin.public-page.update'), PublicPage::defaults())->assertRedirect(route('login'));
        foreach (['guru', 'kepala_sekolah'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            $this->get(route('admin.public-page.edit'))->assertForbidden();
            $this->put(route('admin.public-page.update'), PublicPage::defaults())->assertForbidden();
        }
        $this->assertDatabaseCount('public_pages', 0);
    }

    public function test_admin_changes_are_immediately_public_and_html_is_escaped(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.public-page.edit'))->assertOk();
        $data = array_replace(PublicPage::defaults(), ['title' => 'Judul Sekolah Baru', 'vision' => 'Visi resmi contoh', 'mission' => "Misi satu\nMisi dua", 'principal_name' => 'Nama Kepala Sekolah', 'principal_bio' => '<script>alert(1)</script>']);
        unset($data['principal_photo_path']);
        $this->put(route('admin.public-page.update'), $data + ['principal_photo' => UploadedFile::fake()->image('kepsek.jpg')])->assertSessionHasNoErrors();
        $path = PublicPage::content()->principal_photo_path;
        Storage::disk('public')->assertExists($path);
        $this->app['auth']->forgetGuards();
        $this->get(route('home'))->assertOk()->assertSee('Judul Sekolah Baru')->assertSee('Visi resmi contoh')->assertSee('Misi dua')->assertSee('Nama Kepala Sekolah')->assertSee(asset('storage/'.$path))->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->actingAs(User::where('role', 'admin')->first());
        $this->put(route('admin.public-page.update'), $data)->assertSessionHasNoErrors();
        $this->assertSame($path, PublicPage::content()->principal_photo_path);
        $this->put(route('admin.public-page.update'), $data + ['principal_photo' => UploadedFile::fake()->image('baru.png')])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($path);
        $newPath = PublicPage::content()->principal_photo_path;
        Storage::disk('public')->assertExists($newPath);
        $this->put(route('admin.public-page.update'), $data + ['remove_photo' => '1'])->assertSessionHasNoErrors();
        $this->assertNull(PublicPage::content()->principal_photo_path);
        Storage::disk('public')->assertMissing($newPath);
        $this->assertDatabaseCount('public_pages', 1);
    }

    public function test_invalid_content_and_non_image_uploads_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->put(route('admin.public-page.update'), array_replace(PublicPage::defaults(), ['title' => '', 'principal_photo' => UploadedFile::fake()->create('file.txt', 10, 'text/plain')]))->assertSessionHasErrors(['title', 'principal_photo']);
        $this->assertDatabaseCount('public_pages', 0);
    }
}
