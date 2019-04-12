<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    const ID = 'id';
    const EMAIL = 'email';
    const NAME = 'name';
    const PASSWORD = 'password';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                static::ID => 1,
                static::EMAIL =>'superadmin@gmail.com',
                static::NAME => 'Super Admin',
                static::PASSWORD => Hash::make('qwerty123'),
                static::CREATED_AT => '2018-01-01 10:00:00',
                static::UPDATED_AT => '2018-01-01 10:00:00'
            ],
            [
                static::ID => 2,
                static::EMAIL =>'admin@gmail.com',
                static::NAME => 'Admin Admin',
                static::PASSWORD => Hash::make('qwerty123'),
                static::CREATED_AT => '2018-01-01 10:00:00',
                static::UPDATED_AT => '2018-01-01 10:00:00'
            ]
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
