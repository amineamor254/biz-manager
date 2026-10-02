# Implementation Guide - SaaS Transformation

## 📋 Step-by-Step Implementation (8 Weeks)

This guide follows the roadmap outlined in SAAS_ARCHITECTURE.md with detailed tasks.

---

## PHASE 1: Foundation (Week 1-2) - MVP

### Week 1: Database & Models Setup

#### Day 1-2: Run Migrations
```bash
# Create backup first
php artisan migrate:rollback

# Run new migrations in order
php artisan migrate

# Seed plans
php artisan db:seed --class=PlanSeeder
php artisan db:seed --class=AdminUserSeeder
```

#### Day 3: Update Existing Models
- ✅ User.php - Added workspace relationships
- ✅ Client.php - Added BelongsToWorkspace trait
- ✅ Product.php - Added BelongsToWorkspace trait
- ✅ Invoice.php - Added BelongsToWorkspace trait
- [ ] InvoiceItem.php - Test relationships still work

#### Day 4: Test Models
```php
// Test basic relationships
$workspace = Workspace::first();
$clients = $workspace->clients; // Should work with auto-scoping
$invoices = $workspace->invoices;

// Test user relationships
$user = User::first();
$workspaces = $user->workspaces; // Should return all member workspaces
$owned = $user->ownedWorkspaces; // Should return owned workspaces
```

#### Day 5: Create Helpers
- ✅ Add app/Helpers/SaasHelpers.php
- [ ] Register in composer.json autoload
```json
{
  "autoload": {
    "files": ["app/Helpers/SaasHelpers.php"]
  }
}
```

### Week 2: Middleware & Routes

#### Day 6: Register Middleware
In `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... existing middleware
    'workspace' => \App\Http\Middleware\WorkspaceMiddleware::class,
    'subscription' => \App\Http\Middleware\SubscriptionMiddleware::class,
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'workspace.role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

#### Day 7: Create SaaS Routes
Create `routes/workspace.php`:
```php
Route::middleware(['auth', 'workspace'])->prefix('workspace/{workspace:slug}')->group(function () {
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('workspace.dashboard');
    
    // Clients
    Route::resource('clients', ClientController::class);
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Invoices
    Route::resource('invoices', InvoiceController::class);
    
    // Team
    Route::get('team', [TeamController::class, 'index'])->name('workspace.team');
    Route::post('team/invite', [TeamController::class, 'invite'])->name('workspace.team.invite');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'show'])->name('workspace.settings');
});
```

Register in `routes/web.php`:
```php
include 'workspace.php';
```

#### Day 8: Create Admin Routes
Create `routes/admin.php`:
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('workspaces', AdminWorkspaceController::class);
    Route::resource('subscriptions', AdminSubscriptionController::class);
});
```

#### Day 9-10: Basic Controllers
Create placeholder controllers:
- [ ] DashboardController (SaaS dashboard)
- [ ] ClientController (workspace-scoped)
- [ ] AdminDashboardController
- [ ] TeamController (workspace team management)

---

## PHASE 2: Subscriptions & Billing (Week 3)

### Day 11: Create Subscription Controllers

#### SubscriptionController.php
```php
class SubscriptionController
{
    public function show(Request $request) { }
    public function upgrade(Request $request) { }
    public function downgrade(Request $request) { }
    public function cancel(Request $request) { }
}
```

### Day 12: Stripe Integration Setup
- [ ] Install `laravel/cashier` or `laravel/paddle`
```bash
composer require laravel/cashier
php artisan vendor:publish --tag=cashier-migrations
```

- [ ] Configure Stripe keys in .env
- [ ] Create Stripe webhook routes

### Day 13-14: Implement Subscription Flow
- [ ] Payment form views
- [ ] Webhook handlers for:
  - Payment success
  - Subscription updated
  - Subscription canceled
- [ ] Email notifications

### Day 15: Plan Limits Validation
- [ ] Create ValidationService
- [ ] Implement plan limit checks
- [ ] Add validation on invoice creation (max invoices per month)
- [ ] Add validation on client creation (max clients)

---

## PHASE 3: Team & RBAC (Week 4)

### Day 16-17: Team Management
- [ ] TeamController (invite, remove, update role)
- [ ] Team invitation views
- [ ] Role-based view permissions
- [ ] Accept/decline invitation flow

### Day 18-19: Authorization Policies
Create Policy classes:
```bash
php artisan make:policy WorkspacePolicy --model=Workspace
php artisan make:policy ClientPolicy --model=Client
php artisan make:policy InvoicePolicy --model=Invoice
```

Implement can methods:
- viewAny()
- view()
- create()
- update()
- delete()

### Day 20: Update Views
- [ ] Add role-based UI (hide delete buttons for viewers)
- [ ] Add team member list
- [ ] Add invite form

---

## PHASE 4: AI Features (Week 5-6)

### Day 21-22: AI Service Integration
- [ ] Set up OpenAI/Claude API keys
- [ ] Implement AiService methods
- [ ] Create queue jobs for async report generation

### Day 23-24: Report Generation
- [ ] ReportController
- [ ] Report views/UI
- [ ] Caching mechanism
- [ ] Report expiration handling

### Day 25-26: Dashboard Insights
- [ ] Real-time metric calculations
- [ ] AI-enhanced insights display
- [ ] Trend predictions

### Day 27-28: Async Job Processing
- [ ] Queue configuration (.env)
- [ ] Job scheduling (daily insights)
- [ ] Email notifications for reports

---

## PHASE 5: Admin Panel (Week 7)

### Day 29-30: Admin Dashboard
- [ ] Global analytics
- [ ] User management
- [ ] Workspace management
- [ ] Subscription analytics

### Day 31-32: Admin Actions
- [ ] Manual plan changes
- [ ] User creation/suspension
- [ ] Workspace activation/deactivation
- [ ] Revenue reports

---

## PHASE 6: API & Polish (Week 8+)

### Day 33-34: API Setup
```bash
php artisan install:api
```

- [ ] Token authentication
- [ ] API routes
- [ ] Rate limiting by plan
- [ ] API documentation

### Day 35-36: Testing
- [ ] Unit tests for Services
- [ ] Feature tests for multi-tenancy
- [ ] Integration tests

### Day 37+: Polish & Deploy
- [ ] Performance optimization
- [ ] Security audit
- [ ] Documentation
- [ ] Staging deployment
- [ ] Production deployment

---

## 🔍 TESTING CHECKLIST

### Multi-Tenancy Tests
- [ ] User A can't see User B's data
- [ ] Invoice numbers are unique per workspace
- [ ] Global queries respect workspace scope
- [ ] Middleware prevents cross-workspace access

### Subscription Tests
- [ ] Free plan enforces client limit
- [ ] Basic plan allows upgrades
- [ ] Pro plan has all features
- [ ] Plan downgrade works correctly

### Authorization Tests
- [ ] Only owner can delete workspace
- [ ] Accountant can edit invoices
- [ ] Viewer can't create clients
- [ ] Admin sees all workspaces

---

## ⚠️ MIGRATION STRATEGY FOR EXISTING DATA

If you have existing production data:

```bash
# 1. Backup database
mysqldump -u root business_manager > backup.sql

# 2. Run migrations (adds new columns)
php artisan migrate

# 3. Create data migration
php artisan make:migration migrate_existing_data_to_multitenancy
```

In the migration:
```php
public function up()
{
    // For each existing user, create workspace
    foreach (User::all() as $user) {
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'name' => $user->name . "'s Workspace",
            'slug' => Str::slug($user->name) . '-' . $user->id,
        ]);

        // Migrate their data
        $user->clients()->update(['workspace_id' => $workspace->id]);
        $user->products()->update(['workspace_id' => $workspace->id]);
        $user->invoices()->update(['workspace_id' => $workspace->id]);

        // Add to workspace_users
        WorkspaceUser::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        // Create Free subscription
        Subscription::create([
            'workspace_id' => $workspace->id,
            'plan_id' => Plan::where('slug', 'free')->first()->id,
        ]);
    }
}
```

---

## 📦 DEPLOYMENT CHECKLIST

- [ ] Environment variables configured (.env)
- [ ] Database backups created
- [ ] Migrations tested locally
- [ ] All tests passing
- [ ] Stripe/payment keys configured
- [ ] AI service keys configured (OpenAI/Claude)
- [ ] Queue worker setup
- [ ] Redis cache configured
- [ ] Email service configured
- [ ] Storage configured
- [ ] SSL certificate ready
- [ ] CDN configured (optional)
- [ ] Monitoring/logging setup
- [ ] Error tracking (Sentry)

---

## 🚀 GO-LIVE STRATEGY

### Week Before
- [ ] Full testing on staging
- [ ] Team training on new UI
- [ ] Customer communication prepared

### Day Before
- [ ] Final backups
- [ ] Deployment runbook prepared
- [ ] On-call schedule set

### Launch Day
1. Deploy to staging final time
2. Deploy to production (off-peak hours)
3. Monitor error logs closely
4. Be ready to rollback

### Post-Launch
- [ ] Monitor for 24 hours
- [ ] Check critical workflows
- [ ] Customer support on standby
- [ ] Gather feedback

---

## 📞 SUPPORT CONTACTS

- Database Issues: Database admin
- Stripe Issues: Stripe support
- OpenAI Issues: OpenAI support
- Deployment Issues: DevOps team

---

**Next**: Start with Phase 1, Day 1: Run migrations
