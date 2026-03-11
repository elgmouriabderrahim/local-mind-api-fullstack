<?php

namespace App\Providers;

use App\Models\Question;
use App\Models\Response;
use App\Policies\QuestionPolicy;
use App\Policies\ResponsePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Question::class, QuestionPolicy::class);
        Gate::policy(Response::class, ResponsePolicy::class);
    }
}
