<?php

declare(strict_types=1);

namespace App\Enums;

enum ToothPositionEnum: string
{
    // Permanent teeth (FDI notation)
    case Tooth11 = '11'; // Upper right central incisor
    case Tooth12 = '12'; // Upper right lateral incisor
    case Tooth13 = '13'; // Upper right canine
    case Tooth14 = '14'; // Upper right first premolar
    case Tooth15 = '15'; // Upper right second premolar
    case Tooth16 = '16'; // Upper right first molar
    case Tooth17 = '17'; // Upper right second molar
    case Tooth18 = '18'; // Upper right third molar

    case Tooth21 = '21'; // Upper left central incisor
    case Tooth22 = '22'; // Upper left lateral incisor
    case Tooth23 = '23'; // Upper left canine
    case Tooth24 = '24'; // Upper left first premolar
    case Tooth25 = '25'; // Upper left second premolar
    case Tooth26 = '26'; // Upper left first molar
    case Tooth27 = '27'; // Upper left second molar
    case Tooth28 = '28'; // Upper left third molar

    case Tooth31 = '31'; // Lower left central incisor
    case Tooth32 = '32'; // Lower left lateral incisor
    case Tooth33 = '33'; // Lower left canine
    case Tooth34 = '34'; // Lower left first premolar
    case Tooth35 = '35'; // Lower left second premolar
    case Tooth36 = '36'; // Lower left first molar
    case Tooth37 = '37'; // Lower left second molar
    case Tooth38 = '38'; // Lower left third molar

    case Tooth41 = '41'; // Lower right central incisor
    case Tooth42 = '42'; // Lower right lateral incisor
    case Tooth43 = '43'; // Lower right canine
    case Tooth44 = '44'; // Lower right first premolar
    case Tooth45 = '45'; // Lower right second premolar
    case Tooth46 = '46'; // Lower right first molar
    case Tooth47 = '47'; // Lower right second molar
    case Tooth48 = '48'; // Lower right third molar

    // Primary (deciduous) teeth (FDI notation)
    case Tooth51 = '51'; // Upper right central incisor
    case Tooth52 = '52'; // Upper right lateral incisor
    case Tooth53 = '53'; // Upper right canine
    case Tooth54 = '54'; // Upper right first molar
    case Tooth55 = '55'; // Upper right second molar

    case Tooth61 = '61'; // Upper left central incisor
    case Tooth62 = '62'; // Upper left lateral incisor
    case Tooth63 = '63'; // Upper left canine
    case Tooth64 = '64'; // Upper left first molar
    case Tooth65 = '65'; // Upper left second molar

    case Tooth71 = '71'; // Lower left central incisor
    case Tooth72 = '72'; // Lower left lateral incisor
    case Tooth73 = '73'; // Lower left canine
    case Tooth74 = '74'; // Lower left first molar
    case Tooth75 = '75'; // Lower left second molar

    case Tooth81 = '81'; // Lower right central incisor
    case Tooth82 = '82'; // Lower right lateral incisor
    case Tooth83 = '83'; // Lower right canine
    case Tooth84 = '84'; // Lower right first molar
    case Tooth85 = '85'; // Lower right second molar

    public function label(): string
    {
        return match ($this) {
            self::Tooth11 => 'Tooth 11 - Upper Right Central Incisor',
            self::Tooth12 => 'Tooth 12 - Upper Right Lateral Incisor',
            self::Tooth13 => 'Tooth 13 - Upper Right Canine',
            self::Tooth14 => 'Tooth 14 - Upper Right First Premolar',
            self::Tooth15 => 'Tooth 15 - Upper Right Second Premolar',
            self::Tooth16 => 'Tooth 16 - Upper Right First Molar',
            self::Tooth17 => 'Tooth 17 - Upper Right Second Molar',
            self::Tooth18 => 'Tooth 18 - Upper Right Third Molar',

            self::Tooth21 => 'Tooth 21 - Upper Left Central Incisor',
            self::Tooth22 => 'Tooth 22 - Upper Left Lateral Incisor',
            self::Tooth23 => 'Tooth 23 - Upper Left Canine',
            self::Tooth24 => 'Tooth 24 - Upper Left First Premolar',
            self::Tooth25 => 'Tooth 25 - Upper Left Second Premolar',
            self::Tooth26 => 'Tooth 26 - Upper Left First Molar',
            self::Tooth27 => 'Tooth 27 - Upper Left Second Molar',
            self::Tooth28 => 'Tooth 28 - Upper Left Third Molar',

            self::Tooth31 => 'Tooth 31 - Lower Left Central Incisor',
            self::Tooth32 => 'Tooth 32 - Lower Left Lateral Incisor',
            self::Tooth33 => 'Tooth 33 - Lower Left Canine',
            self::Tooth34 => 'Tooth 34 - Lower Left First Premolar',
            self::Tooth35 => 'Tooth 35 - Lower Left Second Premolar',
            self::Tooth36 => 'Tooth 36 - Lower Left First Molar',
            self::Tooth37 => 'Tooth 37 - Lower Left Second Molar',
            self::Tooth38 => 'Tooth 38 - Lower Left Third Molar',

            self::Tooth41 => 'Tooth 41 - Lower Right Central Incisor',
            self::Tooth42 => 'Tooth 42 - Lower Right Lateral Incisor',
            self::Tooth43 => 'Tooth 43 - Lower Right Canine',
            self::Tooth44 => 'Tooth 44 - Lower Right First Premolar',
            self::Tooth45 => 'Tooth 45 - Lower Right Second Premolar',
            self::Tooth46 => 'Tooth 46 - Lower Right First Molar',
            self::Tooth47 => 'Tooth 47 - Lower Right Second Molar',
            self::Tooth48 => 'Tooth 48 - Lower Right Third Molar',

            self::Tooth51 => 'Tooth 51 - Upper Right Central Incisor (Primary)',
            self::Tooth52 => 'Tooth 52 - Upper Right Lateral Incisor (Primary)',
            self::Tooth53 => 'Tooth 53 - Upper Right Canine (Primary)',
            self::Tooth54 => 'Tooth 54 - Upper Right First Molar (Primary)',
            self::Tooth55 => 'Tooth 55 - Upper Right Second Molar (Primary)',

            self::Tooth61 => 'Tooth 61 - Upper Left Central Incisor (Primary)',
            self::Tooth62 => 'Tooth 62 - Upper Left Lateral Incisor (Primary)',
            self::Tooth63 => 'Tooth 63 - Upper Left Canine (Primary)',
            self::Tooth64 => 'Tooth 64 - Upper Left First Molar (Primary)',
            self::Tooth65 => 'Tooth 65 - Upper Left Second Molar (Primary)',

            self::Tooth71 => 'Tooth 71 - Lower Left Central Incisor (Primary)',
            self::Tooth72 => 'Tooth 72 - Lower Left Lateral Incisor (Primary)',
            self::Tooth73 => 'Tooth 73 - Lower Left Canine (Primary)',
            self::Tooth74 => 'Tooth 74 - Lower Left First Molar (Primary)',
            self::Tooth75 => 'Tooth 75 - Lower Left Second Molar (Primary)',

            self::Tooth81 => 'Tooth 81 - Lower Right Central Incisor (Primary)',
            self::Tooth82 => 'Tooth 82 - Lower Right Lateral Incisor (Primary)',
            self::Tooth83 => 'Tooth 83 - Lower Right Canine (Primary)',
            self::Tooth84 => 'Tooth 84 - Lower Right First Molar (Primary)',
            self::Tooth85 => 'Tooth 85 - Lower Right Second Molar (Primary)',
        };
    }

    public function isPrimary(): bool
    {
        $position = (int) ($this->value);

        return $position >= 51 && $position <= 85;
    }

    public function isPermanent(): bool
    {
        return ! $this->isPrimary();
    }
}
