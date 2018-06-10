<?php

namespace Solunes\Notification\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class TruncateSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Solunes\Notification\App\PttTransactionPayment::truncate();
        \Solunes\Notification\App\PttTransaction::truncate();
    }
}