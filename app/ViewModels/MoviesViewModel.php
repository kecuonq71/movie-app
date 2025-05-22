<?php

namespace App\ViewModels;

use Spatie\ViewModels\ViewModel;
use Carbon\Carbon;

class MoviesViewModel extends ViewModel
{
    public $popularMovies;
    public $nowPlayingMovies;
    public $genres;

    public function __construct($popularMovies, $nowPlayingMovies, $genres)
    {
        $this->popularMovies = $popularMovies;
        $this->nowPlayingMovies = $nowPlayingMovies;
        $this->genres = $genres;
    }

    public function popularMovies()
    {
        return $this->formatMovies($this->popularMovies);
    }

    public function nowPlayingMovies()
    {
        return $this->formatMovies($this->nowPlayingMovies);
    }

    public function genres()
    {
        return $this->genres;
    }


    public function formatMovies($movies)
    {

        return collect($movies)->map(function ($movie) {

            $genresFormatted = collect($movie['genre_ids'])->map(function ($value) {
                return $this->genres()->get($value);
            });

            return collect($movie)->merge([
                'poster_path' => 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'],
                'vote_average' => ($movie['vote_average'] * 10) . '%',
                'release_date' => Carbon::parse($movie['release_date'])->format('M d, Y'),
                'genres' => $genresFormatted->implode(', '), // Kết hợp danh sách genres
            ])->only([
                'id',
                'poster_path',
                'title',
                'vote_average',
                'overview',
                'release_date',
                'genres',
            ]);
        });
    }
}
