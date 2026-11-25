<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'demo@flipplay.test'],
            [
                'name' => 'FlipPlay Demo User',
                'password' => 'password',
            ],
        );

        $media = collect([
            [
                'title' => 'Frieren: Beyond Journey’s End',
                'category' => 'Anime',
                'genre' => 'Fantasy · Adventure',
                'synopsis' => 'Penyihir elf memulai perjalanan baru untuk memahami arti waktu, kenangan, dan hubungan manusia.',
                'release_year' => 2023,
                'cover_url' => null,
            ],
            [
                'title' => 'Blue Lock',
                'category' => 'Manga',
                'genre' => 'Sports · Action',
                'synopsis' => 'Para penyerang muda bersaing dalam program latihan yang dirancang untuk melahirkan striker terbaik.',
                'release_year' => 2018,
                'cover_url' => null,
            ],
            [
                'title' => 'Twenty-Five Twenty-One',
                'category' => 'Drama',
                'genre' => 'Romance · Coming of Age',
                'synopsis' => 'Persahabatan dan mimpi tumbuh di tengah perubahan hidup para remaja pada akhir 1990-an.',
                'release_year' => 2022,
                'cover_url' => null,
            ],
            [
                'title' => 'Classroom of the Elite',
                'category' => 'Light Novel',
                'genre' => 'Psychological · School',
                'synopsis' => 'Siswa di sekolah elite menghadapi persaingan akademis, strategi, dan rahasia di balik sistem kelas.',
                'release_year' => 2015,
                'cover_url' => null,
            ],
        ]);

        $mediaIds = $media->map(
            fn (array $attributes): int => Media::query()->updateOrCreate(
                ['title' => $attributes['title']],
                $attributes,
            )->id,
        );

        $user->media()->syncWithoutDetaching($mediaIds->take(2)->all());
    }
}
