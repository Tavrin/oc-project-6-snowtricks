<?php


namespace App\Enum;


class TricksEnum
{
    public const TRICKS = [
        [
            'name' => 'Mute',
            'description' => 'La main avant saisit la pointe des pieds entre les orteils ou devant le pied avant. Les variations incluent le Mute Stiffy, dans lequel une prise de silence est effectuée tout en redressant les deux jambes, ou alternativement, certains snowboarders attraperont muet et feront pivoter le bord avant de 90 degrés.',
            'group' => "Grab"
        ],
        [
            'name' => 'Indy',
            'description' => "Un indy grab, généralement appelé simplement indy, est un trick de skateboard. C'est un trick aérien de la catégorie des grabs durant lequel le skateur saisit le milieu de sa planche, entre ses deux pieds, sur le côté où pointent ses orteils.
            L'indy est effectué depuis les années '70. Il est généralement utilisé en rampe (half-pipes...), bien qu'il soit également possible de le faire en flat (sur un sol plat). Ce grab est un des tricks de base de la pratique en vert' et il est généralement combiné à des rotations, des kicklips et des heelflips.
            À l'origine, le trick était appelé Indy Air. Inventé par Duane Peters et popularisé par Tony Alva, il consistait à faire un backside air (180° au-dessus d'une rampe, en faisant face à celle-ci) tout en attrapant sa planche du côté des orteils avec la main arrière. Ayant fait du chemin de skateur à skateur, la figure a donné naissance à de nombreuses variantes, poussant les limites de la créativité des skateboarders. Une des variantes les plus célèbres est le kickflip indy.
            Le nom indy grab est apparu à cause d'un manque de connaissance de la distinction entre un indy air et un frontside air. L'utilisation de cette appellation pour nommer le grab en lui-même vient d'une erreur de nomenclature. Parce qu'il est erronément appelé ainsi dans le jeu vidéo Tony Hawk's Pro Skater, beaucoup de gens appellent dorénavant indy le simple fait d'attraper sa planche de cette manière, sans tenir compte du sens de la rotation, et même s'il n'y a pas de rotations du tout.
            ",
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