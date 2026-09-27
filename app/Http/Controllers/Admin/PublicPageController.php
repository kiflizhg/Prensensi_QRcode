<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicPageController extends Controller
{
    public function edit()
    {
        return view('admin.public-page.edit', ['page' => PublicPage::content()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quote' => ['required', 'string', 'max:500'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'vision' => ['nullable', 'string', 'max:5000'],
            'mission' => ['nullable', 'string', 'max:10000'],
            'guide' => ['required', 'string', 'max:10000'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_bio' => ['nullable', 'string', 'max:5000'],
            'principal_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
            'content_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'guide_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_guide_photo' => ['nullable', 'boolean'],
            'guide_caption' => ['nullable', 'string', 'max:500'],
            'remove_content_photo' => ['nullable', 'boolean'],
            'content_caption' => ['nullable', 'string', 'max:500'],
            'principal_caption' => ['nullable', 'string', 'max:500'],
            'whatsapp_url' => ['nullable', 'url:http,https', 'max:2048'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:2048'],
            'website_url' => ['nullable', 'url:http,https', 'max:2048'],
        ]);
        $page = PublicPage::find(1) ?? new PublicPage(PublicPage::defaults());
        $page->id = 1;
        $oldPaths = [$page->principal_photo_path, $page->content_photo_path, $page->guide_photo_path];
        unset($data['guide_photo'], $data['remove_guide_photo']);
        unset($data['principal_photo'], $data['remove_photo'], $data['content_photo'], $data['remove_content_photo']);
        $newPaths = [];
        try {
            foreach (['principal_photo' => 'remove_photo', 'content_photo' => 'remove_content_photo', 'guide_photo' => 'remove_guide_photo'] as $field => $removeField) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('public-page', 'public');
                    if (! $path) {
                        throw \Illuminate\Validation\ValidationException::withMessages([$field => 'Foto gagal disimpan. Silakan coba kembali.']);
                    }
                    $newPaths[] = $path;
                    $data[$field.'_path'] = $path;
                } elseif ($request->boolean($removeField)) {
                    $data[$field.'_path'] = null;
                }
            }
            $page->fill($data)->save();
        } catch (\Throwable $exception) {
            foreach ($newPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }
        foreach ($oldPaths as $oldPath) {
            if ($oldPath && ! in_array($oldPath, [$page->principal_photo_path, $page->content_photo_path, $page->guide_photo_path], true)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        return back()->with('success', 'Konten berhasil disimpan dan langsung tampil di halaman publik.');
    }
}
