<?php


namespace App\Enum;


class GroupEnum
{
    public const TRICK_GROUPS = [
        [
            'name' => 'Grab',
            'description' => "Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. »
            Il existe plusieurs types de grabs selon la position de la saisie et la main choisie pour l'effectuer, avec des difficultés variables"
        ],
        [
            'name' => 'Rotation',
            'description' => "On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations verticales sont des flips. Le principe est d'effectuer une rotation
             horizontale pendant le saut, puis d'attérir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués"
        ],
        [
            'name' => 'Flip',
            'description' => "Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les back flips, rotations en arrière.
             Il est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation. Les flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip...),
             mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées.
             Néanmoins, en dépit de la difficulté technique relative d'une telle figure, le danger de retomber sur la tête ou la nuque est réel et conduit certaines stations de ski à interdire de telles figures dans ses snowparks."
        ],
        [
            'name' => 'Rotation Désaxée',
            'description' => "Une rotation désaxée est une rotation initialement horizontale mais lancée avec un mouvement des épaules particulier qui désaxe la rotation.
             Il existe différents types de rotations désaxées (corkscrew ou cork, rodeo, misty, etc.) en fonction de la manière dont est lancé le buste.
              Certaines de ces rotations, bien qu'initialement horizontales, font passer la tête en bas."
        ],
        [
            'name' => 'Slide',
            'description' => "Un slide consiste à glisser sur une barre de slide. Le slide se fait soit avec la planche dans l'axe de la barre, soit perpendiculaire,
             soit plus ou moins désaxé.
             On peut slider avec la planche centrée par rapport à la barre (celle-ci se situe approximativement au-dessous des pieds du rideur), mais aussi en nose slide,
             c'est-à-dire l'avant de la planche sur la barre, ou en tail slide, l'arrière de la planche sur la barre."
        ],
        [
            'name' => 'One Foot Trick',
            'description' => "Figures réalisée avec un pied décroché de la fixation, afin de tendre la jambe correspondante pour mettre en évidence
             le fait que le pied n'est pas fixé. Ce type de figure est extrêmement dangereuse pour les ligaments du genou en cas de mauvaise réception."
        ],
        [
            'name' => 'Old School',
            'description' => "e terme old school désigne un style de freestyle caractérisée par en ensemble de figure et une manière de réaliser des figures passée de mode,
             qui fait penser au freestyle des années 1980 - début 1990 (par opposition à new school)."
        ],
    ];

    public function getAllGroups(): array
    {
        return self::TRICK_GROUPS;
    }

    public function getGroup(string $groupName): ?array
    {
        foreach (self::TRICK_GROUPS as $group) {
            if ($groupName === $group['name']) {
                return $group;
            }
        }

        return null;
    }
}