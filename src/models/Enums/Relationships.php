<?php

enum Relationships: string
{
    case SIGNIFICANT_OTHER = 'significant-other';
    case FRIEND = 'friend';
    case NIGHT_STAND = 'one-night-stand';
    case ANY = 'anything';
}