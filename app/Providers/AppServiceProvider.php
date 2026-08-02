<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\NumberHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('n', function ($expression) {
            return "<?php echo \\App\\Helpers\\NumberHelper::format($expression); ?>";
        });
    }
}
