<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Model\UserGender;

class UpdateUserGender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserGender::whereIn('gender_name', ['Couple', 'Other'])->delete();
        UserGender::firstOrCreate([
            'gender_name' => 'Trans'
        ]);
    }
}
