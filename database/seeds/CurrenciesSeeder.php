<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{

    public function run()
    {
        $currencies = [
            [
                "source" => "EUR",
                "target" => "USD",
                "exchange_rate" => 1.1956
            ],
            [
                "source" => "EUR",
                "target" => "CHF",
                "exchange_rate" => 1.1689
            ],
            [
                "source" => "EUR",
                "target" => "GBP",
                "exchange_rate" => 0.8848
            ],
            [
                "source" => "USD",
                "target" => "JPY",
                "exchange_rate" => 111.4500
            ],
            [
                "source" => "CHF",
                "target" => "USD",
                "exchange_rate" => 1.0223
            ],
            [
                "source" => "GBP",
                "target" => "CAD",
                "exchange_rate" => 1.6933
            ]
        ];

        foreach($currencies as $currency){
            DB::table('currencies')->insert([
                '_id'           => Uuid::generate()->string,
                'source'        => (string) $currency['source'],
                'target'        => (string) $currency['target'],
                'exchange_rate' => $currency['exchange_rate'],
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'    => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
