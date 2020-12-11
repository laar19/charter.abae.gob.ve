<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'           => 'admin',
            'email'          => 'admin@email',
            'password'       =>  Hash::make('admin'),
            'role'           => 'admin',
            'remember_token' =>  rand()
        ]);
    }
}
