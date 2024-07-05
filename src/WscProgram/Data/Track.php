<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

enum Track: string
{
    case UxWorkshops = 'UX Workshops';
    case BusinessTrack = 'Business Track';
    case JavascriptWorkshops = 'Javascript Workshops';
    case PhpWorkshops = 'PHP Workshops';
    case SymfonyWorkshops = 'Symfony Workshops';
    case ConferenceTalks = 'Conference Talks';
}
