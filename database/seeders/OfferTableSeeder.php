<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offers = [
            [
                'id' => Offer::makeId('Gratuit'),
                'title' => 'Gratuit',
                'price' => 0 ,
                'state' => 0 ,
                'image' => 'anato.png'
            ]
            /*
            ,
            [
                'id' => Offer::makeId('Standard'),
                'title' => 'Standard',
                'price' => 1500 ,
                'state' => 0 ,
                'image' => 'anato.png'
            ],
            [
                'id' => Offer::makeId('Premium'),
                'title' => 'Premium',
                'price' => 5000 ,
                'state' => 0 ,
                'image' => 'anato.png'
            ]
            */
        ];
        $document = new Image(['title' => 'anato.png']);
        $document->save();
        foreach ($offers as $offer){
            $offer = new Offer($offer);
            $offer->save();
        }
    }
}
