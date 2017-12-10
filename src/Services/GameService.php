<?php

namespace Epic\Services;

use Epic\Entities\Game;
use Epic\Entities\Mark;
use Epic\Entities\Team;

class GameService
{
    protected $matrix;
    protected $game;

    public function get($x, $y)
    {
        return $this->matrix[(int)$x][(int)$y];
    }

    private function registerTeamMarks(Team $team)
    {
        foreach ($team->marks as $mark) {
            if ($mark->x != NULL && $mark->x >= 0 && $mark->x < $this->game->gridWidth && $mark->y >= 0 && $mark->y != NULL && $mark->y < $this->game->gridHeight) {
                $this->registerMark($mark);
            }
        }
    }

    public function registerMark(Mark $mark)
    {
        $this->matrix[$mark->x][$mark->y] = $mark;
    }

    private function constructMatrix($width, $height)
    {
        $matrix = new \SplFixedArray($width);

        foreach ($matrix as $i => $_) {
            $matrix[$i] = new \SplFixedArray($height);
        }

        return $matrix;
    }

    public function __construct(Game $game)
    {
        $this->game = $game;
        $this->matrix = $this->constructMatrix($this->game->gridWidth, $this->game->gridHeight);

        $this->registerTeamMarks($game->team1);
        $this->registerTeamMarks($game->team2);
    }

    public function isMatrixFull()
    {
        $defined = 0;

        for ($i = 0; $i < $this->game->gridWidth; $i++) {
            for ($j = 0; $j < $this->game->gridWidth; $j++) {
                if ($this->matrix[$i] && $this->matrix[$i][$j]) {
                    $defined += 1;
                }
            }
        }

        return ($defined == $this->game->gridWidth * $this->game->gridHeight);
    }

    public function areCoordinatesAlreadyFilled($x, $y)
    {
        return $this->matrix[$x] && $this->matrix[$x][$y];
    }
}
