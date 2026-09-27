<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicPage extends Model
{
    protected $guarded = ['id'];

    public static function content(): self
    {
        return static::find(1) ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'quote' => 'Hargai setiap menit, hadir tepat waktu, dan berikan yang terbaik hari ini.',
            'title' => 'Waktu yang tertata, pendidikan yang bermakna.',
            'description' => 'Selamat datang di portal presensi SMK Islam Cipasung. Kelola kehadiran, periksa jadwal, dan ikuti informasi sekolah dalam satu tempat.',
            'vision' => null,
            'mission' => null,
            'guide' => "Masuk menggunakan username dan kata sandi yang diberikan admin sekolah.\nBuka Jadwal Saya untuk memeriksa hari dan jam kehadiran.\nPindai kartu QR di terminal sekolah saat datang dan pulang.\nPeriksa hasil pencatatan melalui Presensi Saya dan Riwayat Presensi.\nAjukan izin, sakit, cuti, atau dinas luar melalui menu Pengajuan beserta lampiran yang diperlukan.\nBaca arahan sekolah dan perbarui identitas melalui menu Profil.",
            'principal_name' => null,
            'principal_bio' => null,
            'principal_photo_path' => null,
            'content_photo_path' => null,
            'guide_photo_path' => null,
            'guide_caption' => null,
            'content_caption' => null,
            'principal_caption' => null,
            'whatsapp_url' => null,
            'instagram_url' => null,
            'website_url' => null,
        ];
    }
}
