<?php

use App\DokumentStatus;

/*
 * Helpers
 */
function ucitajPoVrsti($vrsta)
{
    return DokumentStatus::pda()
                            ->whereIn('vrijednost', ['S', 'D'])
                            ->with(['dokument' => function ($query) use ($vrsta) {
                                $query->whereIn('id_vrsta', $vrsta->pluck('id_vrsta'));
                            }])
                            ->get()
                            ->filter(function ($item) {
                                // samo gdje ima dokument
                                return !is_null($item->dokument);
                            });
}
