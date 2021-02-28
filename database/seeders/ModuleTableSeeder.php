<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $images = ['anato.png' , 'epidemio.png' , 'psychiatrie.png'];
        $modules = [
            [
                'id' => Module::makeId('Anatomie'),
                'title' => 'Anatomie',
                'year' => 1 ,
                'image' => 'anato.png'
            ] ,
            [
                'id' => Module::makeId('Épidémiologie'),
                'title' => 'Épidémiologie',
                'year' => 1 ,
                'image' => 'epidemio.png'
            ] ,
            [
                'id' => Module::makeId('Psychiatrie'),
                'title' => 'Psychiatrie',
                'year' => 1 ,
                'image' => 'psychiatrie.png'
            ],
            [
                'id' => Module::makeId('Épidémiologie 2'),
                'title' => 'Épidémiologie 2',
                'year' => 2 ,
                'image' => 'epidemio.png'
            ],
            [
                'id' => Module::makeId('Psychiatrie 2'),
                'title' => 'Psychiatrie 2',
                'year' => 2 ,
                'image' => 'psychiatrie.png'
            ],
            [
                'id' => Module::makeId('Anatomie 3'),
                'title' => 'Anatomie 3',
                'year' => 3 ,
                'image' => 'anato.png'
            ]
        ];
        foreach ($images as $image){
            $document = new Image(['title' => $image]);
            $document->save();
        }

        foreach ($modules as $module){
            $module = new Module($module);
            $module->save();
        }



    }
}
