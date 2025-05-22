<?php

namespace App\ViewModels;

use Carbon\Carbon;
use Spatie\ViewModels\ViewModel;

class MovieViewModel extends ViewModel
{
    public $movie;

    public function __construct($movie)
    {
        $this->movie = $movie;
    }

    public function movie()
    {
        return collect($this->movie)->merge([
            'poster_path' => 'https://image.tmdb.org/t/p/w500' . ($this->movie['poster_path'] ?? ''),
            'vote_average' => isset($this->movie['vote_average']) ? ($this->movie['vote_average'] * 10) . '%' : 'N/A',
            'release_date' => isset($this->movie['release_date']) ? Carbon::parse($this->movie['release_date'])->format('M d, Y') : 'Unknown',
            'genres' => collect($this->movie['genres'] ?? [])->pluck('name')->implode(', '),

            'crew' => collect(optional($this->movie['credits'])['crew'] ?? [])->take(5)->map(function ($crew) {
                return [
                    'name' => $crew['name'] ?? '',
                    'job' => $crew['job'] ?? '',
                ];
            }),

            'videos' => collect(optional($this->movie['videos'])['results'] ?? [])->take(1)->map(function ($video) {
                return [
                    'key' => $video['key'] ?? '',
                    'site' => $video['site'] ?? '',
                ];
            }),

            'cast' => collect(optional($this->movie['credits'])['cast'] ?? [])->take(5),


            'images' => collect(optional($this->movie['images'])['backdrops'] ?? [])->take(5)->map(function ($image) {
                return [
                    'file_path' => 'https://image.tmdb.org/t/p/w500' . $image['file_path'],
                ];
            }),
            'profile_path' => $this->movie['profile_path'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->movie['title'] ?? 'Unknown'),

        ])->only([
            'id',
            'poster_path',
            'title',
            'vote_average',
            'overview',
            'release_date',
            'genres',
            'crew',
            'cast',
            'images',
            'videos',
            'profile_path',
        ]);
    }
}
