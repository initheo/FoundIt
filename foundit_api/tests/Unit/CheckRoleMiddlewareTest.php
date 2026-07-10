<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckRoleMiddlewareTest extends TestCase
{
    private CheckRole $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckRole();
    }

    public function test_unauthenticated_request_expecting_json()
    {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $response = $this->middleware->handle($request, function () {
            $this->fail('Next closure should not be called');
        }, 'admin');

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'message' => 'Unauthorized: Authentication required']),
            $response->getContent()
        );
    }

    public function test_unauthenticated_request_expecting_html()
    {
        $request = Request::create('/admin/dashboard', 'GET');

        $response = $this->middleware->handle($request, function () {
            $this->fail('Next closure should not be called');
        }, 'admin');

        $this->assertTrue($response->isRedirection());
    }

    public function test_authenticated_user_with_incorrect_role_expecting_json()
    {
        $user = User::factory()->make(['role' => 'user']);
        
        $request = Request::create('/api/admin-endpoint', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->headers->set('Accept', 'application/json');

        $response = $this->middleware->handle($request, function () {
            $this->fail('Next closure should not be called');
        }, 'admin');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'message' => 'Unauthorized: Insufficient permissions']),
            $response->getContent()
        );
    }

    public function test_authenticated_user_with_incorrect_role_expecting_html()
    {
        $user = User::factory()->make(['role' => 'user']);
        
        $request = Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Unauthorized: Insufficient permissions');

        $this->middleware->handle($request, function () {
            $this->fail('Next closure should not be called');
        }, 'admin');
    }

    public function test_authenticated_user_with_correct_role_passes()
    {
        $user = User::factory()->make(['role' => 'admin']);
        
        $request = Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $called = false;
        $response = $this->middleware->handle($request, function ($req) use (&$called) {
            $called = true;
            return response('Passed');
        }, 'admin');

        $this->assertTrue($called);
        $this->assertEquals('Passed', $response->getContent());
    }
}
