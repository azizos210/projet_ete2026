<?php

namespace App\Enum;

enum StatutTicketEnum: string
{
    case OUVERT    = 'ouvert';
    case EN_COURS  = 'en_cours';
    case RESOLU    = 'resolu';
    case ESCALADE  = 'escalade';
}
