<?php

use Illuminate\Database\Seeder;

class PostulateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('postulates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $postulates = Storage::get('data/postulates.csv');
        $postulates = explode("\n", $postulates);
        print_r($postulates);

        foreach ($postulates as $postulate) {
            echo "INSERT -- " . $postulate . " --- \n";
            if(empty(trim($postulate))){
                continue;
            }
            $postulateExplode = explode(",", trim($postulate));
            $postulateObj = new \App\Postulate();
            $postulateObj->district = $postulateExplode[0];
            $postulateObj->municipality = $postulateExplode[1];
            $postulateObj->total_population = $postulateExplode[2];
            $postulateObj->presidency = $postulateExplode[3];
            $postulateObj->regidurias = $postulateExplode[4];
            $postulateObj->sindicaturas = $postulateExplode[5];
            $postulateObj->total = $postulateExplode[6];
            $postulateObj->municipality_key = $postulateExplode[7];
            $postulateObj->save();
        }
    }
}
