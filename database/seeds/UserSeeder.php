<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'name' => 'Rifa Online',
            'email' => 'admin@gmail.com',
            'telephone' => '41996285726',
            'status' => '1',
            'password' => bcrypt('admin123@@#'),
        ]);

        DB::table('consulting_environments')->insert([
            [
                'name' => 'Rifa Online',
                'facebook' => 'teste',
                'instagram' => 'teste',
                'key_pix' => 'APP_USR-5261151288450206-070203-6d3620d89d07ea1a4b47999ea3b80252-781237219',
                'key_pix_public' => 'APP_USR-97569547-7cfd-484a-9d4a-12e964395cf3',
                'user_id' => $user1->id
            ]
        ]);
    }
}
