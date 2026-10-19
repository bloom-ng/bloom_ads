<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class FetchCurrencyRatesRouteTest extends TestCase
{
    public function test_runs_fetch_with_configured_password(): void
    {
        config(['services.currency_fetch.password' => 'secret-pass']);
        Artisan::shouldReceive('call')->once()->with('currency:fetch-rates')->andReturn(0);

        $this->get('/fetch-currency-rates?password=secret-pass')
            ->assertOk()
            ->assertJson(['message' => 'Currency rates fetched successfully!']);
    }

    public function test_rejects_wrong_password(): void
    {
        config(['services.currency_fetch.password' => 'secret-pass']);
        Artisan::shouldReceive('call')->never();

        $this->get('/fetch-currency-rates?password=wrong')->assertUnauthorized();
    }

    public function test_rejects_everything_when_no_password_is_configured(): void
    {
        config(['services.currency_fetch.password' => null]);
        Artisan::shouldReceive('call')->never();

        $this->get('/fetch-currency-rates')->assertUnauthorized();
        $this->get('/fetch-currency-rates?password=')->assertUnauthorized();
    }
}
