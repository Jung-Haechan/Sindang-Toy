<?php

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use GuzzleHttp\Client;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

});

Route::get('newsCount', function (Request $request) {
    if ($request->names) {
        $names = explode(' ', $request->names);
    } else {
        $names = ['이준석', '한동훈', '이재명', '이낙연'];
    }
    $colorMap = [
        '이준석' => '#162343',
        '한동훈' => '#E61E2B',
        '이재명' => '#00A0E2',
        '이낙연' => '#017B45',
    ];
    $dates = [];
    for ($i = 3; $i >= 0; $i--) {
        $dates[] = Carbon::now()->subDays($i)->format('Y-m-d');
    }
    $datasets = [];
    foreach ($names as $name) {
        $d = [];
        foreach ($dates as $date) {
            $d[] = Article::where('title', 'like', "%{$name}%")->where('created_at', 'like', "{$date}%")->count();
        }
        $datasets[] = [
            'label' => $name,
            'backgroundColor' => $colorMap[$name] ?? '#777777',
            'borderColor' => $colorMap[$name] ?? '#777777',
            'data' => $d,
        ];
    }
    return [
        'labels' => $dates,
        'datasets' => $datasets
    ];
});

Route::post('/getFrom', function (Request $request) {
    $method = $request->all()['method'];
    $url = $request->all()['url'];
    $authorization = $request->header('Authorization');
    $client = new Client([
        'base_uri' => 'http://api-main.rallypoint.kr/v1/',
        'headers' => [
            'Authorization' => $authorization,
        ]
    ]);

    if ($method === 'post') {
        $data = $request->all()['data'];
        $res = $client->request($method, $url, [
            'form_params' => $data,
        ]);
    } else {
        $res = $client->request($method, $url, [
            ''
        ]);
    }
    $result = json_decode($res->getBody())->data;

    return $result;
});
