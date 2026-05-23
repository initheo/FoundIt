<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Item;
use App\Models\Claim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
        ]);
    }

    /**
     * Test guest is redirected to login page when accessing dashboard.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test regular user cannot access admin dashboard.
     */
    public function test_regular_user_cannot_access_dashboard(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test admin user can access dashboard.
     */
    public function test_admin_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * Test admin can view categories page.
     */
    public function test_admin_can_view_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    /**
     * Test admin can login with valid credentials.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => $this->adminUser->email,
            'password' => 'password', // default factory password is 'password' or hashed value
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    /**
     * Test regular user login is rejected on admin login.
     */
    public function test_regular_user_login_is_rejected_on_admin_login(): void
    {
        $response = $this->post('/admin/login', [
            'email' => $this->regularUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test admin can logout.
     */
    public function test_admin_can_logout(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/logout');
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    /**
     * Test admin can update user profile and password without confirmation.
     */
    public function test_admin_can_update_user_password_without_confirmation(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($this->adminUser)->put("/admin/users/{$user->id}", [
            'name' => 'Updated User Name',
            'email' => 'updated@student.uisi.ac.id',
            'role' => 'user',
            'password' => 'newpassword123', // single field, no password_confirmation
        ]);

        $response->assertRedirect(route('admin.users.show', $user->id));
        $response->assertSessionHasNoErrors();
        
        $this->assertTrue(\Hash::check('newpassword123', $user->fresh()->password));
        $this->assertEquals('Updated User Name', $user->fresh()->name);
    }
}
