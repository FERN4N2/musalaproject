<?php

namespace Database\Seeders;

use App\Models\Gateway;
use App\Models\Peripheral;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            $item = new Gateway([
                'serial' => 'Serial' . $i . '_' . Str::random(5),
                'name' => Str::random(10),
                'ipv4' => $i . '.' . $i . '.' . $i . '.' . $i,
            ]);
            $item->save();
            for ($j = 1; $j <= 4; $j++) {
                $child =  new Peripheral([
                    'status' => true,
                    'created' => Carbon::now()->toDateTimeString(),
                    'vendor' => 'Vendor ' . Str::random(5),
                    'uid' => $i*100 + $j*10,
                    'id_gateway' => $item->_id,
                ]);
                $child->save();
            }
        }
    }
}
