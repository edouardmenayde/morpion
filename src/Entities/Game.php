<?php

namespace Epic\Entities;

abstract class GameType
{
    const classic = 'classic';
    const advanced = 'advanced';
}

class Game
{
    public $id;
    public $startedAt;
    public $initialPoints;
    public $initialDoubleAttack;
    public $maxDoubleAttack;
    public $gridWidth;
    public $gridHeight;
    public $team1Id;
    public $team2Id;
    public $winnerId;
    public $ended;
    public $type;

    public $team1;
    public $team2;
    public $winner;
}
