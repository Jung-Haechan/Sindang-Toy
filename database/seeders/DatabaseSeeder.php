<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $authorization = 'NWDpLMygzWtow1JM2UNAG9iTJPcY6mMyVZhbpGeKJfIbCKdQ101D3A98uXLl4JafgocIlppD8oX7VUWrcErAiXeQEuySRlJgJoj2S5t3aBPIv7quc0LZ7ybIqcnyheAC';
        $client = new Client([
            'base_uri' => 'http://api-main.rallypoint.kr/v1/',
            'headers' => [
                'Authorization' => $authorization,
            ]
        ]);

        $res = $client->request('get', "modules/byCategory?category=지역");
        $result = json_decode(json_decode($res->getBody())->data);
        foreach ($result as $region) {
            foreach ($region->rx_documents as $doc) {
                $user = User::updateOrCreate([
                    'srl' => $doc->rx_member->member_srl,
                ], [
                    'uuid' => $doc->rx_member->uuid,
                    'name' => $doc->rx_member->nick_name,
                    'is_admin' => $doc->rx_member->is_admin === 'Y',
                ]);
            }
        }
        $users = User::whereNotNull('uuid')->whereNull('registered_at')->get();
        foreach ($users as $user) {
            $res = $client->request('get', "members/?g=793.&gx=2141&tr={$user->uuid}&t2=2024-01-05T02:27:16.383Z&timestamp=Fri%20Jan%2005%202024%2011:27:16%20GMT+0900%20(%ED%95%9C%EA%B5%AD%20%ED%91%9C%EC%A4%80%EC%8B%9C)&");
            $result = json_decode(json_decode($res->getBody())->data);
            $user->update([
                'registered_at' => Carbon::createFromFormat('YmdHis', $result->regdate)->format('Y-m-d H:i:s'),
                'age' => max($result->age_range, 0),
                'gender' => $result->gender,
            ]);
        }
    }
}
