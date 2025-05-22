<?php

namespace App\ViewModels;

use Spatie\ViewModels\ViewModel;
use Carbon\Carbon;

class ActorViewModel extends ViewModel
{
    public $actor;
    public $social;
    public $credits;

    public function __construct($actor, $social, $credits)
    {
        $this->actor = $actor;
        $this->social = $social;
        $this->credits = $credits;
    }

    public function actor()
    {
        return collect($this->actor)->merge([
            'profile_path' => $this->actor['profile_path']
                ? 'https://image.tmdb.org/t/p/w500' . $this->actor['profile_path']
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->actor['name'] ?? 'Unknown'),

            'age' => isset($this->actor['birthday']) ? Carbon::parse($this->actor['birthday'])->age : null,

            'birthday' => isset($this->actor['birthday']) ? Carbon::parse($this->actor['birthday'])->format('M d, Y') : 'Unknown',

            'place_of_birth' => $this->actor['place_of_birth'] ?? 'Unknown',

            'biography' => $this->actor['biography'] ?? 'No biography available.',
            'homepage' => $this->actor['homepage'] ?? null,
        ])->only([
            'id',
            'profile_path',
            'name',
            'birthday',
            'age',
            'place_of_birth',
            'biography',
            'homepage',
        ]);
    }

    public function social()
    {
        return [
            'twitter' => isset($this->social['twitter']) && $this->social['twitter']
                ? 'https://twitter.com/' . $this->social['twitter']
                : null,

            'facebook' => isset($this->social['facebook']) && $this->social['facebook']
                ? 'https://www.facebook.com/' . $this->social['facebook']
                : null,

            'instagram' => isset($this->social['instagram']) && $this->social['instagram']
                ? 'https://www.instagram.com/' . $this->social['instagram']
                : null,
        ];
    }

    public function knownForMovies()
    {
        $castMovies = collect($this->credits)->get('cast');

        return collect($castMovies)->sortByDesc('popularity')->take(5)->map(function($movie) {
            if (isset($movie['title'])) {
                $title = $movie['title'];
            } elseif (isset($movie['name'])) {
                $title = $movie['name'];
            } else {
                $title = 'Untitled';
            }

            return collect($movie)->merge([
                'poster_path' => $movie['poster_path']
                    ? 'https://image.tmdb.org/t/p/w185'.$movie['poster_path']
                    : 'https://via.placeholder.com/185x278',
                'title' => $title,
                'linkToPage' => $movie['media_type'] === 'movie' ? route('movies.show', $movie['id']) : route('tv.show', $movie['id'])
            ])->only([
                'poster_path', 'title', 'id', 'media_type', 'linkToPage',
            ]);
        });
    }


    public function credits()
    {
        $castMovies = collect($this->credits)->get('cast');

        return collect($castMovies)->map(function($movie) {
            if (isset($movie['release_date'])) {
                $releaseDate = $movie['release_date'];
            } elseif (isset($movie['first_air_date'])) {
                $releaseDate = $movie['first_air_date'];
            } else {
                $releaseDate = '';
            }

            if (isset($movie['title'])) {
                $title = $movie['title'];
            } elseif (isset($movie['name'])) {
                $title = $movie['name'];
            } else {
                $title = 'Untitled';
            }

            return collect($movie)->merge([
                'release_date' => $releaseDate,
                'release_year' => isset($releaseDate) ? Carbon::parse($releaseDate)->format('Y') : 'Future',
                'title' => $title,
                'character' => isset($movie['character']) ? $movie['character'] : '',
                'linkToPage' => $movie['media_type'] === 'movie' ? route('movies.show', $movie['id']) : route('tv.show', $movie['id']),
            ])->only([
                'release_date', 'release_year', 'title', 'character', 'linkToPage',
            ]);
        })->sortByDesc('release_date');
    }
}