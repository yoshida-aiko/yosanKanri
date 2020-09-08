<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         //$this->call(BudgetsTableSeeder::class);
         //$this->call(UsersTableSeeder::class);
         //$this->call(CartsTableSeeder::class);
         $this->call(ConditionsTableSeeder::class);
    }
}
