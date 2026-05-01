<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\SeasonalPromoService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplySeasonalPromos extends Command
{
    protected $signature   = 'promos:seasonal-apply';
    protected $description = 'Active les promos saisonnières sur les produits is_promo=true, et désactive les promos expirées.';

    public function handle(SeasonalPromoService $service): void
    {
        // 1. Désactiver les promos dont la date de fin est passée
        $expired = Product::whereNotNull('promo_price')
            ->whereNotNull('promo_ends_at')
            ->where('promo_ends_at', '<', now())
            ->get();

        foreach ($expired as $product) {
            $product->update([
                'promo_price'   => null,
                'promo_ends_at' => null,
                'is_promo'      => false,
            ]);
        }

        if ($expired->count()) {
            $this->info("✓ {$expired->count()} promo(s) expirée(s) désactivée(s).");
        }

        // 2. Activer les promos de l'événement en cours
        $event = $service->currentEvent();

        if (!$event) {
            $this->info('Aucun événement saisonnier en cours.');
            return;
        }

        $this->info("Événement actif : {$event['name']} (jusqu'au {$event['end_date']})");

        $products = Product::where('is_promo', true)
            ->whereNull('promo_price')
            ->get();

        $rate = ($event['discount_min'] + $event['discount_max']) / 2 / 100;

        foreach ($products as $product) {
            $product->update([
                'promo_price'   => round($product->price * (1 - $rate)),
                'promo_ends_at' => Carbon::parse($event['end_date'])->endOfDay(),
            ]);
        }

        $this->info("✓ {$products->count()} produit(s) mis en promotion à -{$event['discount_min']}–{$event['discount_max']}%.");
    }
}
