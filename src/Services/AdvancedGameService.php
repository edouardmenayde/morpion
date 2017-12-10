<?php

namespace Epic\Services;

use Epic\Entities\MarkModelType;

class AdvancedGameService extends GameService
{
    private function getLegalAttacks($mark)
    {
        $legalAttacks = [];
        if ($mark->x == 0 && $mark->y == 0) {
            array_push($legalAttacks, [$mark->x + 1, $mark->y]);
            array_push($legalAttacks, [$mark->x, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y + 1]);
        } else if ($mark->x == $this->game->gridWidth - 1 && $this->game->gridHeight - 1) {
            array_push($legalAttacks, [$mark->x - 1, $mark->y]);
            array_push($legalAttacks, [$mark->x, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x - 1, $mark->y - 1]);
        } else if ($mark->y == 0) {
            array_push($legalAttacks, [$mark->x - 1, $mark->y]);
            array_push($legalAttacks, [$mark->x - 1, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y]);
        } else if ($mark->y == $this->game->gridHeight - 1) {
            array_push($legalAttacks, [$mark->x - 1, $mark->y]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y]);
        } else if ($mark->x == 0) {
            array_push($legalAttacks, [$mark->x, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x, $mark->y]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x, $mark->y + 1]);
        } else if ($mark->x == $this->game->gridWidth - 1) {
            array_push($legalAttacks, [$mark->x, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x - 1, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x, $mark->y]);
            array_push($legalAttacks, [$mark->x - 1, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x, $mark->y + 1]);
        } else {
            array_push($legalAttacks, [$mark->x - 1, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y - 1]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y]);
            array_push($legalAttacks, [$mark->x + 1, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x - 1, $mark->y + 1]);
            array_push($legalAttacks, [$mark->x - 1, $mark->y]);
        }

        return $legalAttacks;
    }

    public function isIllegalPlacement($markId, $x, $y)
    {
        $alreadyPlaced = false;
        foreach ($this->matrix as $line) {
            foreach ($line as $mark) {
                if ($mark) {
                    if ($mark->id == $markId) {
                        $alreadyPlaced = true;
                    }
                }
            }
        }

        if ($alreadyPlaced) {
            return true;
        }

        return $this->areCoordinatesAlreadyFilled($x, $y);
    }

    public function isIllegalAttack($mark, $x, $y)
    {
        $target = $this->get($x, $y);

        if (!$target) {
            return true;
        }

        if ($mark->markModel->type == MarkModelType::archer) {
            return true;
        }

        if ($target->teamId == $mark->teamId) {
            return true;
        }

        if ($mark->hp <= 0 || $target->hp <= 0) {
            return true;
        }

        $legalAttacks = $this->getLegalAttacks($mark);

        $illegal = true;

        foreach ($legalAttacks as $legalAttack) {
            if ($legalAttack[0] == $x && $legalAttack[1] == $y) {
                $illegal = false;
            }
        }
        return $illegal;
    }

    public function isIllegalSpell($mark, $x, $y)
    {
        $target = $this->get($x, $y);

        if (!$target) {
            return true;
        }

        if ($mark->markModel->type != MarkModelType::wizard) {
            return true;
        }

        if ($target->teamId == $mark->teamId) {
            return true;
        }

        if ($mark->hp <= 0 || $target->hp <= 0) {
            return true;
        }

        if ($mark->mana < 2) {
            return true;
        }

        return false;
    }

    public function isIllegalHealing($mark, $x, $y)
    {
        $target = $this->get($x, $y);

        if (!$target) {
            return true;
        }

        if ($mark->markModel->type != MarkModelType::wizard) {
            return true;
        }

        if ($mark->hp <= 0 || $target->hp + 3 > $target->markModel->hp) {
            return true;
        }

        if ($mark->mana < 1) {
            return true;
        }

        return false;
    }

    public function isIllegalArmageddon($mark, $x, $y)
    {
        $target = $this->get($x, $y);

        if (!$target) {
            return true;
        }

        if ($mark->markModel->type != MarkModelType::wizard) {
            return true;
        }

        if ($target->teamId == $mark->teamId) {
            return true;
        }

        if ($mark->hp <= 0) {
            return true;
        }

        if ($mark->mana < 5) {
            return true;
        }


        return false;
    }

    public function isIllegalArrowAttack($mark, $x, $y)
    {
        $target = $this->get($x, $y);

        if (!$target) {
            return true;
        }

        if ($mark->markModel->type != MarkModelType::archer) {
            return true;
        }

        if ($target->teamId == $mark->teamId) {
            return true;
        }

        if ($mark->hp <= 0 || $target->hp <= 0) {
            return true;
        }

        return false;
    }

    public function getWinner()
    {
        $team1HP = array_reduce($this->game->team1->marks, function ($hp, $mark) {
            return $hp + ($mark->hp >= 0 ? $mark->hp : 0);
        }, 0);

        $team2HP = array_reduce($this->game->team2->marks, function ($hp, $mark) {
            return $hp + ($mark->hp >= 0 ? $mark->hp : 0);
        }, 0);

        if ($team1HP == 0 && $team2HP > 0) {
            return $this->game->team2->id;
        } elseif ($team2HP == 0 && $team1HP > 0) {
            return $this->game->team1->id;
        } else {
            return false;
        }
    }

    public function isGameEnded()
    {
        return $this->getWinner();
    }
}
