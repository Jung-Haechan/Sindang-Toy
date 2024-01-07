<?php

namespace Database\Seeders;

use App\Models\Press;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPHtmlParser\Dom;

class PressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = new Client([
            'base_uri' => 'https://media.naver.com/press/',
        ]);
        for ($i = 0; $i < 200; $i++) {
            $pid = str_pad($i, 3, 0, STR_PAD_LEFT);
            try {
                $res = $client->request('get', "{$pid}?sid=100");
                $dom = new Dom;
                $dom->loadStr($res->getBody()->getContents());
                $name_e = $dom->find('.press_hd_name_link')[0];
                $name = $name_e->text;
                Press::firstOrCreate([
                    'pid' => $pid,
                ], [
                    'label' => $name,
                ]);
            } catch (\Exception $e) {
                print_r("{$pid}: {$e->getCode()} || ");
            }
        }
    }
}
