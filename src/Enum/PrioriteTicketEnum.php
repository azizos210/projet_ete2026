<?php

namespace App\Enum;

enum PrioriteTicketEnum: string
{
    case BASSE  = 'basse';
    case NORMALE = 'normale';
    case HAUTE  = 'haute';
    case URGENTE = 'urgente';
}
