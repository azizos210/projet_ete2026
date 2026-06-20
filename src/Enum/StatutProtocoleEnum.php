<?php

namespace App\Enum;

enum StatutProtocoleEnum: string
{
    case BROUILLON = 'brouillon';
    case ACTIF     = 'actif';
    case ARCHIVE   = 'archive';
}
