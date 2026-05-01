<?php

/*
|--------------------------------------------------------------------------
| Promotions Saisonnières — FBK Printing
|--------------------------------------------------------------------------
|
| Événements à dates fixes (Noël/Rentrée) : définis par mois/jour, recalculés
| chaque année automatiquement.
|
| Événements islamiques (Ramadan, Tabaski) : dates absolues à mettre à jour
| chaque année car calqués sur le calendrier lunaire.
|
| discount_min / discount_max : fourchette appliquée aux produits is_promo=true
|
*/

return [

    'events' => [

        'fin_annee' => [
            'name'         => 'Fêtes de fin d\'année',
            'icon'         => '🎄',
            'color'        => '#dc2626',   // rouge
            'bg'           => '#fef2f2',
            'border'       => '#fca5a5',
            'start'        => ['month' => 12, 'day' => 10],
            'end'          => ['month' => 1,  'day' => 10],
            'discount_min' => 20,
            'discount_max' => 30,
        ],

        'rentree' => [
            'name'         => 'Rentrée scolaire',
            'icon'         => '📚',
            'color'        => '#2563eb',   // bleu
            'bg'           => '#eff6ff',
            'border'       => '#93c5fd',
            'start'        => ['month' => 9, 'day' => 1],
            'end'          => ['month' => 10, 'day' => 15],
            'discount_min' => 15,
            'discount_max' => 25,
        ],

        /*
        |------------------------------------------------------------------
        | Ramadan & Tabaski — à mettre à jour chaque année
        |------------------------------------------------------------------
        | Ramadan 2026  : ~18 fév – 19 mars
        | Tabaski 2026  : ~27 mai – 5 juin
        */
        'ramadan' => [
            'name'         => 'Ramadan Moubarak',
            'icon'         => '🌙',
            'color'        => '#16a34a',   // vert
            'bg'           => '#f0fdf4',
            'border'       => '#86efac',
            'start_date'   => '2026-02-18',
            'end_date'     => '2026-03-19',
            'discount_min' => 20,
            'discount_max' => 30,
        ],

        'tabaski' => [
            'name'         => 'Tabaski',
            'icon'         => '🐑',
            'color'        => '#d97706',   // ambre
            'bg'           => '#fffbeb',
            'border'       => '#fcd34d',
            'start_date'   => '2027-04-27',
            'end_date'     => '2027-05-05',
            'discount_min' => 20,
            'discount_max' => 30,
        ],

    ],

];
