<?php

namespace App\Console\Commands;

use App\Http\Controllers\PoiController;
use App\Models\Poi;
use Cache;
use Illuminate\Console\Command;

class ResetPoiUpdatedAt extends Command
{
    protected $signature = 'reset:poi-updated-at
        {--poi= : ID одной публикации}
        {--force : Не спрашивать подтверждение}';

    protected $description = 'Обнуляет updated_at у точек: его каждый день затирал крон обнуления просмотров';

    public function handle()
    {
        $query = Poi::query()->whereNotNull('updated_at');
        if ($this->option('poi')) {
            $query->where('id', $this->option('poi'));
        }

        $count = $query->count();
        if (!$count) {
            $this->info('Нечего сбрасывать: updated_at уже пустой.');

            return 0;
        }

        $this->warn('Настоящие даты правок были затёрты ежедневным кроном, восстановить их нельзя.');
        if (!$this->option('force') && !$this->confirm("Обнулить updated_at у {$count} точек?")) {
            $this->info('Отменено.');

            return 1;
        }

        // toBase(), чтобы Eloquent не проставил updated_at заново
        $query->toBase()->update(['updated_at' => null]);

        // Список «Недавно обновлённые» сортируется по updated_at и кэшируется
        Cache::tags(PoiController::POI_LIST_CACHE_KEY)->flush();

        $this->info("Готово: updated_at обнулён у {$count} точек.");

        return 0;
    }
}
