<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Http\Middleware\CustomThrottle;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Mockery;

class CustomThrottleTest extends TestCase
{
    public function test_allows_request_within_limit()
    {
        $limiter = Mockery::mock(RateLimiter::class);
        $limiter->shouldReceive('tooManyAttempts')
                ->with(Mockery::any(), 5)
                ->once()
                ->andReturn(false);

        $limiter->shouldReceive('hit')
                ->with(Mockery::any(), 60)
                ->once();

        $middleware = new CustomThrottle($limiter);
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->status());
    }

    public function test_blocks_request_when_limit_exceeded()
    {
        $limiter = Mockery::mock(RateLimiter::class);
        $limiter->shouldReceive('tooManyAttempts')
                ->with(Mockery::any(), 5)
                ->once()
                ->andReturn(true);

        $limiter->shouldReceive('availableIn')
                ->with(Mockery::any())
                ->once()
                ->andReturn(45);

        $middleware = new CustomThrottle($limiter);
        $request = Request::create('/test', 'GET');

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(429, $response->status());
        $json = $response->getData(true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('Too many requests', $json['message']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
