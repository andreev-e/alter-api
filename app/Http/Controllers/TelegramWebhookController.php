<?php

namespace App\Http\Controllers;

use App\Telegram\UpdateHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, string $token, UpdateHandler $handler): Response
    {
        $expected = (string) config('telegram.bot.api_token');
        if ($expected === '' || !hash_equals($expected, $token)) {
            abort(400);
        }

        try {
            $handler->handle($request->json()->all());
        } catch (Throwable $e) {
            // Telegram повторяет доставку при не-2xx; ошибку логируем, а не отдаём наружу
            report($e);
        }

        return response('OK');
    }
}
