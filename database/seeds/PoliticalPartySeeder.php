<?php

use Illuminate\Database\Seeder;

class PoliticalPartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('politic_parties')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        \App\PoliticParty::insert([
                [
                    'id' => 1,
                    'name' => 'MORENA'
                ],
                [
                    'id' => 2,
                    'name' => 'VERDE'
                ],
                [
                    'id' => 3,
                    'name' => 'PT'
                ],
                [
                    'id' => 4,
                    'name' => 'PSI'
                ],
                [
                    'id' => 5,
                    'name' => 'MORENA/PT'
                ]
            ]

        );


    }
}
