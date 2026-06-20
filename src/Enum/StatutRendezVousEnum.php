<?php

namespace App\Enum;

enum StatutRendezVousEnum: string
{
    case EN_ATTENTE = 'en_attente';
    case CONFIRME   = 'confirme';
    case ANNULE     = 'annule';
    case TERMINE    = 'termine';
    case NO_SHOW    = 'no_show';
}
