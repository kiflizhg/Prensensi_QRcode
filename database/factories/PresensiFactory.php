<?php

namespace Database\Factories;

use App\Models\Presensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tanggal' => fake()->date(),
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'status' => fake()->randomElement(['hadir', 'izin', 'sakit', 'cuti', 'dinas_luar', 'alpa']),
            'kode_qr' => fake()->uuid(),
        ];
    }
}
