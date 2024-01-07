<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Press;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PHPHtmlParser\Dom;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = new Client([
            'base_uri' => 'https://media.naver.com/newsflash/'
        ]);
        $presses = Press::all();
        foreach ($presses as $press) {
            try {
                $lastTimestamp = Carbon::now();
                $timestr = '';
                while (Carbon::now()->subDays(4)->lt($lastTimestamp)) {
                    $res = $client->request('get', "$press->pid/Politics?before=$timestr");
                    $result = json_decode($res->getBody()->getContents());
                    if (empty($result->list)) {
                        continue 2;
                    }
                    foreach ($result->list as $news) {
                        $timestr = $news->serviceTimeForMoreApi;
                        $timestamp = Carbon::createFromFormat('YmdHis', str_replace('00000', '', $timestr))->format('Y-m-d H:i:s');
                        if (Carbon::now()->subDays(4)->gt($timestamp)) {
                            continue 3;
                        }
                        Article::firstOrCreate([
                            'aid' => $news->id,
                        ], [
                            'press_id' => $press->id,
                            'title' => $news->title,
                            'created_at' => $timestamp,
                        ]);
                    }
                    $lastTimestamp = $timestamp;
                }
            } catch (\Exception $e) {
                print_r("{$press->pid}: {$e->getCode()} || ");
            }
        }
    }
}
