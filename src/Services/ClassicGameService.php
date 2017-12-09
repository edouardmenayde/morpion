<?php

namespace Epic\Services;

use Epic\Entities\Game;
use Epic\Entities\Mark;
use Epic\Entities\Team;

class ClassicGameService
{
    private $matrix;
    private $game;

    private function registerTeamMarks(Team $team)
    {
        foreach ($team->marks as $mark) {
            $this->registerMark($mark);
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

    public function getWinner()
    {
        $team1Matches = 0;
        $team2Matches = 0;
        $winner = null;

        // test horizontally
        foreach ($this->matrix as $line) {
            $team1Matches = 0;
            $team2Matches = 0;

            foreach ($line as $item) {
                if ($item) {
                    if ($item->teamId == $this->game->team1->id) {
                        $team1Matches += 1;
                    } else if ($item->teamId == $this->game->team2->id) {
                        $team2Matches += 1;
                    }
                }
            }

            if ($team1Matches == $this->game->gridWidth) {
                $winner = $this->game->team1->id;
            }

            if ($team2Matches == $this->game->gridWidth) {
                $winner = $this->game->team1->id;
            }
        }

        if ($winner) {
            return $winner;
        }

        // test vertically
        for ($j = 0; $j < $this->game->gridHeight; $j++) {
            $team1Matches = 0;
            $team2Matches = 0;

            for ($i = 0; $i < $this->game->gridWidth; $i++) {
                if ($this->matrix[$i] && $this->matrix[$i][$j]) {
                    $item = $this->matrix[$i][$j];

                    if ($item->teamId == $this->game->team1->id) {
                        $team1Matches += 1;
                    } else if ($item->teamId == $this->game->team2->id) {
                        $team2Matches += 1;
                    }
                }
            }

            if ($team1Matches == $this->game->gridHeight) {
                $winner = $this->game->team1->id;
            }

            if ($team2Matches == $this->game->gridHeight) {
                $winner = $this->game->team1->id;

            }
        }

        if ($winner) {
            return $winner;
        }

        $team1Matches = 0;
        $team2Matches = 0;

        // test first diagonal
        // here we assume $gridWidth = $gridHeight
        for ($i = 0; $i < $this->game->gridWidth; $i++) {

            if ($this->matrix[$i] && $this->matrix[$i][$i]) {
                $item = $this->matrix[$i][$i];

                if ($item->teamId == $this->game->team1->id) {
                    $team1Matches += 1;
                } else if ($item->teamId == $this->game->team2->id) {
                    $team2Matches += 1;
                }
            }
        }

        if ($team1Matches == $this->game->gridWidth) {
            $winner = $this->game->team1->id;
        }

        if ($team2Matches == $this->game->gridWidth) {
            $winner = $this->game->team1->id;
        }

        if ($winner) {
            return $winner;
        }

        $team1Matches = 0;
        $team2Matches = 0;

        // test first diagonal
        // here we assume $gridWidth = $gridHeight
        for ($i = 0; $i < $this->game->gridWidth; $i++) {
            if ($this->matrix[$i] && $this->matrix[$i][$this->game->gridHeight - 1 - $i]) {
                $item = $this->matrix[$i][$this->game->gridHeight - 1 - $i];

                if ($item->teamId == $this->game->team1->id) {
                    $team1Matches += 1;
                } else if ($item->teamId == $this->game->team2->id) {
                    $team2Matches += 1;
                }
            }
        }
        
        if ($team1Matches == $this->game->gridWidth) {
            $winner = $this->game->team1->id;
        }

        if ($team2Matches == $this->game->gridWidth) {
            $winner = $this->game->team1->id;
        }

        return $winner;
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

    public function isGameEnded()
    {
        return $this->getWinner() || $this->isMatrixFull();
    }

    public function isIllegalPlacement($x, $y)
    {
        return $this->matrix[$x] && $this->matrix[$x][$y];
    }
}
