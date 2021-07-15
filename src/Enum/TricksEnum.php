<?php


namespace App\Enum;


class TricksEnum
{
    public const TRICKS = [
        [
            'name' => 'Mute',
            'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
            'group' => "Grab"
        ],
        [
            'name' => 'Indy',
            'description' => 'saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
            'group' => "Grab"
        ],
        [
            'name' => 'Big Foot',
            'description' => 'Rotation horizontale de trois tours.',
            'group' => "Rotation"
        ],
        [
            'name' => '180',
            'description' => "Rotation horizontale d'un demi tour",
            'group' => "Rotation"
        ],
        [
            'name' => 'Front flip',
            'description' => 'Rotation verticale en avant.',
            'group' => "Flip"
        ],
        [
            'name' => 'Back flip',
            'description' => 'Rotation verticale en arrière.',
            'group' => "Flip"
        ],
        [
            'name' => 'Cork 360',
            'description' => 'Il consiste à réaliser un 360 en désaxant en arrière. Vous lancez donc votre 360 en lançant vers l’arrière',
            'group' => "Rotation Désaxée"
        ],
        [
            'name' => 'Nose Slide',
            'description' => "Consiste à avoir l'avant de la planche sur la barre",
            'group' => "Slide"
        ],
        [
            'name' => 'Tail Slide',
            'description' => "Consiste à avoir l'arrière de la planche sur la barre.",
            'group' => "Slide"
        ],
        [
            'name' => 'Nose Grab',
            'description' => "Saisie de la partie avant de la planche, avec la main avant.",
            'group' => "Grab"
        ],
    ];
}