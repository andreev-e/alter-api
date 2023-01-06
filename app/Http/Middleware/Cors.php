<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Cors
{
    /**
     * Массив доменов, с которых будем принимать запросы.
     *
     * @var array
     */
    protected $domains = [
        'http://localhost:3000',
        'https://localhost:3000',
        'http://localhost:8100',
    ];

    /**
     * Метод, который обрабатывает все запросы, приходящие на сервер.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // проверим, присутствует ли заголовок HTTP_ORIGIN в запросе
        // и разрешен ли домен
        $origin = $request->headers->get('Origin');
        // if(!$origin || !in_array($origin, $this->domains, true)) {
        //     return new Response('Forbidden', 403);
        // }

        //если есть, то устанавливаем нужные заголовки
        return $next($request)
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header(
                'Access-Control-Allow-Headers',
                'Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type'
            );
    }
}
