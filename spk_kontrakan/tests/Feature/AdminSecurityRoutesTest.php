<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminSecurityRoutesTest extends TestCase
{
    public function test_guest_is_redirected_from_admin_register_page(): void
    {
        $this->get(route('admin.register'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_ensure_role_redirects_guest_to_login(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(false);

        $middleware = new EnsureRole();
        $response = $middleware->handle(Request::create('/admin/register', 'GET'), fn () => response('ok'), 'super_admin');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertSame(route('admin.login'), $response->headers->get('Location'));
    }

    public function test_ensure_role_forbids_non_matching_role(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn((object) ['role' => 'admin']);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');

        $middleware = new EnsureRole();
        $middleware->handle(Request::create('/admin/register', 'GET'), fn () => response('ok'), 'super_admin');
    }

    public function test_ensure_role_allows_matching_role(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn((object) ['role' => 'super_admin']);

        $middleware = new EnsureRole();
        $response = $middleware->handle(Request::create('/admin/register', 'GET'), fn () => response('ok'), 'super_admin');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }

    public function test_guest_is_redirected_from_sensitive_admin_routes(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.backup.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.activity-logs.index'))->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_clear_cache_rejects_get_method(): void
    {
        $this->get('/dashboard/clear-cache')->assertStatus(405);
    }

    public function test_admin_login_post_has_throttle_middleware(): void
    {
        $route = Route::getRoutes()->getByName('admin.login.post');
        $middlewares = $route->gatherMiddleware();

        $this->assertTrue(
            collect($middlewares)->contains(fn (string $item) => str_contains($item, 'throttle:10,1') || str_contains($item, 'ThrottleRequests:10,1')),
            'Route admin.login.post harus memiliki middleware throttle:10,1'
        );
    }

    public function test_admin_register_route_has_super_admin_role_middleware(): void
    {
        $route = Route::getRoutes()->getByName('admin.register');
        $middlewares = $route->gatherMiddleware();

        $this->assertTrue(
            collect($middlewares)->contains(function (string $item) {
                return str_contains($item, 'role:super_admin')
                    || str_contains($item, EnsureRole::class.':super_admin');
            }),
            'Route admin.register harus dibatasi oleh middleware role:super_admin'
        );
    }

    public function test_sensitive_admin_routes_have_super_admin_role_middleware(): void
    {
        foreach ([
            'admin.users.index',
            'admin.backup.index',
            'admin.activity-logs.index',
            'admin.activity-logs.dashboard',
            'admin.activity-logs.security',
            'admin.activity-logs.archive',
            'admin.activity-logs.api-stats',
        ] as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);
            $middlewares = $route->gatherMiddleware();

            $this->assertTrue(
                collect($middlewares)->contains(function (string $item) {
                    return str_contains($item, 'role:super_admin')
                        || str_contains($item, EnsureRole::class.':super_admin');
                }),
                "Route {$routeName} harus dibatasi oleh middleware role:super_admin"
            );
        }
    }

    public function test_review_create_route_is_registered(): void
    {
        $route = Route::getRoutes()->getByName('reviews.create');

        $this->assertNotNull($route);
        $this->assertSame(['GET', 'HEAD'], $route->methods());
    }
}
