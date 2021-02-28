<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(config('admin.admin_name')) {
            $user = User::firstOrCreate(
                ['email' => config('admin.admin_email')], [
                    'name' => config('admin.admin_name'),
                    'password' => bcrypt(config('admin.admin_password')),
                ]
            );
            $user->is_admin = 1;
            $user->save();
            $user->markEmailAsVerified();


        }
        $user = User::firstOrCreate(
            ['email' => 'root2@test.com'], [
                'name' => 'root2',
                'password' => bcrypt(config('admin.admin_password')),
            ]
        );
        $user->is_admin = 1;
        $user->save();
        $user->markEmailAsVerified();

        $user = User::firstOrCreate(
            ['email' => 'root3@test.com'], [
                'name' => 'root3',
                'password' => bcrypt(config('admin.admin_password')),
            ]
        );
        $user->is_admin = 1;
        $user->save();
        $user->markEmailAsVerified();
    }
}
