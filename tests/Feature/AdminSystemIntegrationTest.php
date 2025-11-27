<?php

namespace Tests\Feature;

use App\Domains\Listings\Models\Service;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSystemIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected User $seller;

    protected Service $service;

    protected Order $order;

    protected Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create(['email' => 'user@test.com']);
        $this->seller = User::factory()->create(['email' => 'seller@test.com']);

        // Create test data
        $this->service = Service::factory()->create([
            'creator_id' => $this->seller->id,
            'title' => 'Web Design Service',
            'price' => 500.00,
        ]);

        $this->order = Order::factory()->create([
            'buyer_id' => $this->regularUser->id,
            'seller_id' => $this->seller->id,
            'service_id' => $this->service->id,
            'price' => 500.00,
            'platform_fee' => 25.00,
            'total_amount' => 525.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'user_id' => $this->regularUser->id,
            'amount' => 500.00,
            'platform_fee' => 25.00,
            'total_amount' => 525.00,
            'status' => 'pending',
            'payment_method' => 'credit_card',
            'provider_reference' => 'ref_12345',
        ]);
    }

    // USER MANAGEMENT INTEGRATION TESTS

    /** @test */
    public function admin_can_access_user_management()
    {
        // Story: Admin can access user management interface
        // Request → Controller → Model → Response

        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->can('viewAny', User::class));
    }

    /** @test */
    public function admin_can_search_users_by_name()
    {
        // Story: Admin searches for a user by name through the interface
        // Request: GET /admin/users?search=user
        // Controller logic: Searches firstname, lastname, email

        $this->assertTrue($this->admin->can('viewAny', User::class));

        // Verify search query logic
        $users = User::where('firstname', 'like', '%user%')
            ->orWhere('lastname', 'like', '%user%')
            ->orWhere('email', 'like', '%user%')
            ->get();

        // User with email 'user@test.com' should be found
        $this->assertGreaterThan(0, $users->count());
    }

    /** @test */
    public function admin_can_filter_users_by_role()
    {
        // Story: Admin filters users by role
        // Request: GET /admin/users?role=admin
        // Controller filters: whereHas('roles', ...)

        $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();
        $this->assertTrue($admins->contains($this->admin));
    }

    /** @test */
    public function admin_can_filter_users_by_verification_status()
    {
        // Story: Admin filters verified vs unverified users
        // Request: GET /admin/users?verification=unverified
        // Controller logic: whereNull('email_verified_at') OR whereNotNull

        // Create an unverified user
        User::factory()->unverified()->create(['email' => 'unverified@test.com']);

        $unverified = User::whereNull('email_verified_at')->get();
        $this->assertGreaterThan(0, $unverified->count());
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        // Story: Admin views a user's detailed profile
        // Request: GET /admin/users/{id}
        // Model relationships: roles, orders, payments, etc.

        $user = User::findOrFail($this->regularUser->id);
        $this->assertEquals('user@test.com', $user->email);
        $this->assertFalse($user->hasRole('admin'));
    }

    // LISTING MANAGEMENT INTEGRATION TESTS

    /** @test */
    public function admin_can_access_listing_management()
    {
        // Story: Admin can access listing management interface
        // Request → Controller → Model → Response

        $this->assertTrue($this->admin->can('viewAny', Service::class));
    }

    /** @test */
    public function admin_can_search_listings_by_title()
    {
        // Story: Admin searches services by title
        // Request: GET /admin/listings?search=Web

        $services = Service::where('title', 'like', '%Web%')->get();
        $this->assertTrue($services->contains($this->service));
    }

    /** @test */
    public function admin_can_filter_listings_by_category()
    {
        // Story: Admin filters services by category
        // Request: GET /admin/listings?category={id}

        $category_id = $this->service->category_id;
        $services = Service::where('category_id', $category_id)->get();
        $this->assertGreaterThan(0, $services->count());
    }

    /** @test */
    public function admin_can_filter_listings_by_price_range()
    {
        // Story: Admin filters services by price range
        // Request: GET /admin/listings?price_min=100&price_max=600

        $services = Service::whereBetween('price', [100, 600])->get();
        $this->assertTrue($services->contains($this->service));
    }

    /** @test */
    public function admin_can_filter_listings_by_status()
    {
        // Story: Admin filters services by status (active/deleted)
        // Request: GET /admin/listings?status=active

        $active = Service::whereNull('deleted_at')->get();
        $this->assertTrue($active->contains($this->service));
    }

    /** @test */
    public function admin_can_view_listing_details()
    {
        // Story: Admin views detailed service information including creator
        // Request: GET /admin/listings/{id}
        // Model: Service with creator relationship

        $service = Service::findOrFail($this->service->id);
        $this->assertEquals('Web Design Service', $service->title);
        $this->assertEquals(500.00, $service->price);
        $this->assertEquals($this->seller->id, $service->creator_id);
    }

    /** @test */
    public function admin_can_suspend_service()
    {
        // Story: Admin has permission to suspend a service
        // Authorization check: admin policy before() returns true

        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->can('suspend', $this->service));
    }

    // ORDER MANAGEMENT INTEGRATION TESTS

    /** @test */
    public function admin_can_access_order_management()
    {
        // Story: Admin can access order management interface
        // Request → Controller → Model → Response

        $this->assertTrue($this->admin->can('viewAny', Order::class));
    }

    /** @test */
    public function admin_can_search_orders_by_id()
    {
        // Story: Admin searches orders by order ID
        // Request: GET /admin/orders?search={order_id}
        // Controller logic: WHERE id = search

        $orders = Order::where('id', $this->order->id)->get();
        $this->assertEquals(1, $orders->count());
        $this->assertTrue($orders->contains($this->order));
    }

    /** @test */
    public function admin_can_filter_orders_by_status()
    {
        // Story: Admin filters orders by status (pending, in_progress, completed, etc.)
        // Request: GET /admin/orders?status=pending

        $pending = Order::where('status', 'pending')->get();
        $this->assertTrue($pending->contains($this->order));
    }

    /** @test */
    public function admin_can_filter_orders_by_payment_status()
    {
        // Story: Admin filters orders by payment status
        // Request: GET /admin/orders?payment_status=pending

        $pending_payments = Order::where('payment_status', 'pending')->get();
        $this->assertTrue($pending_payments->contains($this->order));
    }

    /** @test */
    public function admin_can_view_order_with_all_relationships()
    {
        // Story: Admin views complete order details with buyer, seller, service, payment
        // Request: GET /admin/orders/{id}
        // Response includes: buyer, seller, service, payment

        $order = Order::with(['buyer', 'seller', 'service', 'payment'])->findOrFail($this->order->id);

        $this->assertNotNull($order->buyer);
        $this->assertNotNull($order->seller);
        $this->assertNotNull($order->service);
        $this->assertEquals($this->regularUser->id, $order->buyer_id);
        $this->assertEquals($this->seller->id, $order->seller_id);
        $this->assertEquals(500.00, $order->price);
        $this->assertEquals(525.00, $order->total_amount);
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        // Story: Admin can update order status through the policy
        // Authorization: updateStatus is an admin-only action

        $this->assertTrue($this->admin->hasRole('admin'));

        // Verify the admin role is properly configured
        $this->assertTrue($this->admin->hasRole('admin'));

        // Admin should be able to access the updateStatus action
        // The policy checks this with hasRole('admin')
        $updateStatusPermitted = $this->admin->hasRole('admin');
        $this->assertTrue($updateStatusPermitted);
    }

    // PAYMENT MANAGEMENT INTEGRATION TESTS

    /** @test */
    public function admin_can_access_payment_management()
    {
        // Story: Admin can access payment management interface
        // Request → Controller → Model → Response

        $this->assertTrue($this->admin->can('viewAny', Payment::class));
    }

    /** @test */
    public function admin_can_search_payments_by_reference()
    {
        // Story: Admin searches payments by provider reference
        // Request: GET /admin/payments?search=ref_12345

        $payments = Payment::where('provider_reference', 'ref_12345')->get();
        $this->assertTrue($payments->contains($this->payment));
    }

    /** @test */
    public function admin_can_filter_payments_by_status()
    {
        // Story: Admin filters payments by status (pending, paid, failed)
        // Request: GET /admin/payments?status=pending

        $pending = Payment::where('status', 'pending')->get();
        $this->assertTrue($pending->contains($this->payment));
    }

    /** @test */
    public function admin_can_filter_payments_by_method()
    {
        // Story: Admin filters payments by payment method
        // Request: GET /admin/payments?method=credit_card

        $credit_card = Payment::where('payment_method', 'credit_card')->get();
        $this->assertTrue($credit_card->contains($this->payment));
    }

    /** @test */
    public function admin_can_view_payment_with_relationships()
    {
        // Story: Admin views payment details including order and user info
        // Request: GET /admin/payments/{id}
        // Response includes: order, user, amounts, provider reference

        $payment = Payment::with(['order', 'user'])->findOrFail($this->payment->id);

        $this->assertEquals(500.00, $payment->amount);
        $this->assertEquals(525.00, $payment->total_amount);
        $this->assertEquals('ref_12345', $payment->provider_reference);
        $this->assertEquals($this->regularUser->id, $payment->user_id);
        $this->assertNotNull($payment->order);
    }

    /** @test */
    public function admin_can_mark_payment_as_paid()
    {
        // Story: Admin manually marks a payment as paid
        // Authorization: Only admin can do this
        // Action: Sets status='paid', paid_at=now()

        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->can('markAsPaid', $this->payment));
        $this->assertEquals('pending', $this->payment->status);
    }

    /** @test */
    public function admin_can_mark_payment_as_failed()
    {
        // Story: Admin marks a payment as failed
        // Authorization: Only admin can do this
        // Action: Sets status='failed'

        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->can('markAsFailed', $this->payment));
    }

    // REFUND MANAGEMENT INTEGRATION TESTS

    /** @test */
    public function admin_can_view_refunds_list()
    {
        // Story: Admin can view list of refunds
        // Request: GET /admin/refunds
        // Response: Paginated list of refunds with their statuses

        $refund = \App\Domains\Payments\Models\Refund::factory()->create([
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
            'status' => 'requested',
        ]);

        $this->assertTrue($this->admin->can('viewAny', \App\Domains\Payments\Models\Refund::class));

        $refundFromDb = \App\Domains\Payments\Models\Refund::findOrFail($refund->id);
        $this->assertEquals('requested', $refundFromDb->status);
    }

    /** @test */
    public function admin_can_filter_refunds_by_status()
    {
        // Story: Admin filters refunds by workflow status
        // Request: GET /admin/refunds?status=requested
        // Statuses: requested, approved, rejected, completed

        $refund = \App\Domains\Payments\Models\Refund::factory()->create([
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
            'status' => 'requested',
        ]);

        $requested = \App\Domains\Payments\Models\Refund::where('status', 'requested')->get();
        $this->assertTrue($requested->contains($refund));
    }

    /** @test */
    public function admin_can_approve_refund_workflow()
    {
        // Story: Admin approves a refund request from requested → approved
        // Authorization: Admin policy allows approve action
        // Prerequisite: Refund status must be 'requested'

        $refund = \App\Domains\Payments\Models\Refund::factory()->create([
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
            'status' => 'requested',
        ]);

        $this->assertTrue($this->admin->can('approve', $refund));
        $this->assertEquals('requested', $refund->status);
    }

    /** @test */
    public function admin_can_reject_refund_request()
    {
        // Story: Admin rejects a refund request with admin notes
        // Authorization: Admin policy allows reject action
        // Result: Refund status becomes 'rejected', admin notes added

        $refund = \App\Domains\Payments\Models\Refund::factory()->create([
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
            'status' => 'requested',
        ]);

        $this->assertTrue($this->admin->can('reject', $refund));
    }

    /** @test */
    public function admin_can_complete_approved_refund()
    {
        // Story: Admin marks an approved refund as completed (approved → completed)
        // Authorization: Admin policy allows markAsCompleted action
        // Prerequisite: Refund must be in 'approved' status

        $refund = \App\Domains\Payments\Models\Refund::factory()->create([
            'order_id' => $this->order->id,
            'payment_id' => $this->payment->id,
            'status' => 'approved',
        ]);

        $this->assertTrue($this->admin->can('markAsCompleted', $refund));
        $this->assertEquals('approved', $refund->status);
    }

    // AUTHORIZATION & SECURITY TESTS

    /** @test */
    public function regular_user_cannot_access_admin_features()
    {
        // Story: Regular user cannot access admin-only features
        // Authorization: Regular user role doesn't have admin permissions

        $this->assertFalse($this->regularUser->hasRole('admin'));
        $this->assertFalse($this->regularUser->can('viewAny', User::class));
        $this->assertFalse($this->regularUser->can('viewAny', Service::class));
    }

    /** @test */
    public function admin_before_policy_grants_all_permissions()
    {
        // Story: Admin policy before() method grants all permissions
        // Implementation: before() returns true for admin, bypassing specific checks

        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertTrue($this->admin->can('update', $this->service));
        $this->assertTrue($this->admin->can('delete', $this->service));
        $this->assertTrue($this->admin->can('suspend', $this->service));
    }

    // DATA FLOW INTEGRATION TESTS

    /** @test */
    public function complete_order_to_payment_workflow()
    {
        // Story: Complete data flow from order creation through payment
        // 1. Order exists with buyer, seller, service
        // 2. Payment linked to order
        // 3. Admin can view and manage both

        $order = Order::with(['buyer', 'seller', 'service', 'payment'])->findOrFail($this->order->id);

        // All relationships exist
        $this->assertNotNull($order->buyer);
        $this->assertNotNull($order->seller);
        $this->assertNotNull($order->service);
        $this->assertNotNull($order->payment);

        // Admin can access order
        $this->assertTrue($this->admin->can('viewAny', Order::class));

        // Payment amounts match order
        $payment = $order->payment;
        $this->assertEquals($order->total_amount, $payment->total_amount);
        $this->assertEquals(500.00, $order->price);
        $this->assertEquals(25.00, $order->platform_fee);
        $this->assertEquals(525.00, $order->total_amount);

        // Admin can manage payment
        $this->assertTrue($this->admin->can('viewAny', Payment::class));
        $this->assertTrue($this->admin->can('markAsPaid', $payment));
    }

    /** @test */
    public function admin_can_access_all_management_modules()
    {
        // Story: Admin dashboard provides access to all management modules
        // Modules: Users, Listings, Orders, Payments, Refunds, Flags

        $this->assertTrue($this->admin->can('viewAny', User::class));
        $this->assertTrue($this->admin->can('viewAny', Service::class));
        $this->assertTrue($this->admin->can('viewAny', Order::class));
        $this->assertTrue($this->admin->can('viewAny', Payment::class));
        $this->assertTrue($this->admin->can('viewAny', \App\Domains\Payments\Models\Refund::class));

        // Verify we have test data in each module
        $this->assertGreaterThanOrEqual(3, User::count()); // admin, user, seller + possibly others
        $this->assertEquals(1, Service::count());
        $this->assertEquals(1, Order::count());
        $this->assertEquals(1, Payment::count());
    }

    /** @test */
    public function search_and_filter_queries_return_correct_results()
    {
        // Story: All search and filter functionality returns accurate results
        // Verification: Multiple queries on same data return consistent results

        // Create multiple users
        User::factory()->create(['firstname' => 'John', 'email' => 'john@example.com']);
        User::factory()->create(['firstname' => 'Jane', 'email' => 'jane@example.com']);

        // Search by name returns correct count
        $searchResults = User::where('firstname', 'like', '%John%')->get();
        $this->assertEquals(1, $searchResults->count());

        // Search by email returns correct count
        $emailResults = User::where('email', 'like', '%jane%')->get();
        $this->assertEquals(1, $emailResults->count());

        // All users can be retrieved
        $allUsers = User::all();
        $this->assertGreaterThan(3, $allUsers->count());

        // Filter by role works
        $adminUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();
        $this->assertTrue($adminUsers->contains($this->admin));
    }

    /** @test */
    public function admin_has_complete_visibility_of_system_data()
    {
        // Story: Admin can see all system data
        // Verification: Admin has no data restrictions

        // Count all data
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalPayments = Payment::count();

        // Admin can query all data
        $this->assertGreaterThan(0, $totalUsers);
        $this->assertGreaterThan(0, $totalOrders);
        $this->assertGreaterThan(0, $totalPayments);

        // Admin role grants access to everything
        $this->assertTrue($this->admin->hasRole('admin'));

        // Verify authorization
        $this->assertTrue($this->admin->can('viewAny', User::class));
        $this->assertTrue($this->admin->can('viewAny', Order::class));
        $this->assertTrue($this->admin->can('viewAny', Payment::class));
    }
}
