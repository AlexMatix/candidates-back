<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // RECUERDA QUE EL PUTO JSON NO SE GUARDA BIEN Y HAY QUE GENERARLO DESDE EL FRONT Y LUEGO UPTEARLO EN LA TABLA (MAMADAS!)
        \App\User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('secret'),
            'configuration' => '',
            'type' => \App\User::ADMIN
        ]);
    }
}
