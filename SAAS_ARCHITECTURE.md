# SaaS Architecture Plan - Business Manager

## 📋 Executive Overview

This document outlines the transformation of a Laravel CRUD dashboard into a comprehensive multi-tenant SaaS platform with AI-powered insights, subscription management, and role-based access control.

---

## 🏗️ 1. ARCHITECTURE PRINCIPLES

### Multi-Tenancy Model
**Strategy**: Domain-based multi-tenancy with shared database
- Each workspace/tenant has isolated data at application level
- Single database with `workspace_id` foreign key across core tables
- Faster onboarding and easier scaling than separate DBs per tenant

### Data Isolation Layers
1. **Database Layer**: `workspace_id` on all business tables
2. **Application Layer**: Middleware to enforce workspace context
3. **Query Layer**: Automatic scope by current workspace

### Subscription Model
- **Free Plan**: 1 workspace, 10 clients max, basic reports
- **Basic Plan**: 3 workspaces, 100 clients, monthly insights
- **Pro Plan**: Unlimited workspaces, unlimited clients, AI features, API access

---

## 📁 2. IMPROVED FOLDER STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                    # Admin dashboard (separate)
│   │   ├── Dashboard/                # SaaS dashboard controllers
│   │   ├── Api/                      # API controllers (future)
│   │   └── Auth/                     # Authentication
│   ├── Middleware/
│   │   ├── WorkspaceMiddleware.php   # Tenant context
│   │   ├── SubscriptionMiddleware.php# Plan limits
│   │   └── AdminMiddleware.php       # Admin-only routes
│   └── Requests/                     # Form requests
│
├── Models/
│   ├── User.php                      # App user (belongs to workspaces)
│   ├── Workspace.php                 # Tenant/workspace
│   ├── Subscription.php              # Billing info
│   ├── Plan.php                      # Subscription plans
│   ├── WorkspaceUser.php             # Pivot - user/workspace roles
│   ├── Client.php                    # Business entity
│   ├── Product.php                   # Business inventory
│   ├── Invoice.php                   # Transaction
│   ├── InvoiceItem.php               # Line item
│   ├── AiReport.php                  # Cached AI reports
│   └── AuditLog.php                  # Track changes
│
├── Services/                         # Business logic layer
│   ├── WorkspaceService.php          # Workspace management
│   ├── SubscriptionService.php       # Billing/plans
│   ├── AiService.php                 # AI features (reports, insights)
│   ├── ReportService.php             # Report generation
│   ├── InvoiceService.php            # Invoice operations
│   └── ValidationService.php         # Plan-based limits
│
├── Repositories/                     # Data access layer (optional but recommended)
│   ├── ClientRepository.php
│   ├── InvoiceRepository.php
│   ├── ProductRepository.php
│   └── BaseRepository.php            # Auto-scopes by workspace
│
├── Enums/
│   ├── SubscriptionPlan.php          # Free/Basic/Pro
│   ├── UserRole.php                  # Owner/Admin/User/Viewer
│   └── InvoiceStatus.php             # Draft/Sent/Paid/Overdue
│
├── Events/
│   ├── WorkspaceCreated.php
│   ├── SubscriptionUpgraded.php
│   ├── InvoiceGenerated.php
│   └── AiReportGenerated.php
│
├── Listeners/
│   ├── LogAuditTrail.php
│   ├── NotifyUserOnUpgrade.php
│   └── GenerateAiInsights.php
│
├── Mail/
│   ├── SubscriptionConfirmation.php
│   ├── AiReportReady.php
│   └── InvoiceNotification.php
│
├── Jobs/
│   ├── GenerateSalesReport.php       # Queue AI report generation
│   ├── SendAiInsights.php            # Daily insights
│   └── ProcessInvoicePayment.php
│
├── Notifications/
│   ├── NewAiInsight.php
│   └── ReportGenerated.php
│
├── Traits/
│   ├── BelongsToWorkspace.php        # Auto-scopes queries
│   ├── HasPermissions.php            # Role checks
│   └── ValidatesSubscriptionLimits.php
│
├── Policies/
│   ├── WorkspacePolicy.php           # Authorization
│   ├── ClientPolicy.php
│   ├── InvoicePolicy.php
│   └── SubscriptionPolicy.php
│
└── Exceptions/
    ├── PlanLimitExceededException.php
    ├── UnauthorizedWorkspaceAccess.php
    └── InsufficientSubscriptionTierException.php

routes/
├── web.php                           # Public + auth routes
├── workspace.php                     # SaaS dashboard (grouped middleware)
├── admin.php                         # Admin panel (separate)
└── api.php                           # API v1 (future)

resources/
└── views/
    ├── dashboard/                    # SaaS dashboard
    │   ├── workspace/
    │   ├── invoices/
    │   ├── clients/
    │   ├── products/
    │   ├── reports/
    │   └── settings/
    ├── admin/                        # Admin panel
    │   ├── users/
    │   ├── workspaces/
    │   ├── subscriptions/
    │   └── analytics/
    └── auth/                         # Public auth pages

database/
├── migrations/
│   ├── [timestamp]_create_workspaces_table.php
│   ├── [timestamp]_create_subscriptions_table.php
│   ├── [timestamp]_create_plans_table.php
│   ├── [timestamp]_create_workspace_users_table.php
│   ├── [timestamp]_create_users_table.php
│   ├── [timestamp]_create_clients_table.php
│   ├── [timestamp]_create_products_table.php
│   ├── [timestamp]_create_invoices_table.php
│   ├── [timestamp]_create_invoice_items_table.php
│   ├── [timestamp]_create_ai_reports_table.php
│   └── [timestamp]_create_audit_logs_table.php
│
└── seeders/
    ├── PlanSeeder.php                # Free/Basic/Pro
    └── AdminUserSeeder.php

config/
├── saas.php                          # SaaS config (plans, limits)
└── ai.php                            # AI service config

tests/
├── Feature/
│   ├── Workspace/
│   ├── Subscription/
│   ├── Invoice/
│   └── AI/
└── Unit/
    ├── Services/
    └── Traits/
```

---

## 🗄️ 3. DATABASE SCHEMA CHANGES

### New Core Tables

#### `workspaces` (NEW - The Tenant)
```
- id (PK)
- user_id (FK) - workspace owner
- name
- slug (for URL routing)
- domain (custom domain support)
- timezone
- currency (default USD)
- logo_path
- is_active
- created_at, updated_at
```

#### `subscriptions` (NEW - Billing)
```
- id (PK)
- workspace_id (FK, unique)
- plan_id (FK)
- stripe_subscription_id (nullable)
- stripe_customer_id (nullable)
- status (active, paused, cancelled)
- current_period_start
- current_period_end
- trial_ends_at (nullable)
- canceled_at (nullable)
- created_at, updated_at
```

#### `plans` (NEW - Pricing Tiers)
```
- id (PK)
- name (Free/Basic/Pro)
- slug (free/basic/pro)
- price (monthly in cents)
- billing_cycle (monthly/yearly)
- features (JSON)
  - max_workspaces
  - max_clients
  - max_invoices_per_month
  - ai_reports (true/false)
  - api_access (true/false)
  - custom_domain (true/false)
  - priority_support (true/false)
- created_at, updated_at
```

#### `workspace_users` (NEW - Pivot with Roles)
```
- id (PK)
- workspace_id (FK)
- user_id (FK)
- role (owner/admin/accountant/viewer) - using Enum
- invited_at
- accepted_at
- created_at, updated_at
- UNIQUE(workspace_id, user_id)
```

#### `ai_reports` (NEW - Cached Reports)
```
- id (PK)
- workspace_id (FK)
- type (sales_summary/top_products/client_analysis/trend_forecast)
- title
- description
- report_data (JSON - cached AI response)
- generated_by (user_id FK)
- period_start, period_end
- created_at, updated_at
```

#### `audit_logs` (NEW - Compliance)
```
- id (PK)
- workspace_id (FK)
- user_id (FK)
- action (created/updated/deleted)
- model_type (Invoice, Client, etc.)
- model_id
- old_values (JSON - before)
- new_values (JSON - after)
- ip_address
- created_at
```

### Modified Existing Tables

#### `users` - ADD Columns
```
REMOVE: password reset directly from user
ADD:
- workspace_id (nullable) - current active workspace
- role (admin/user) - global role
- verification_code
- is_verified (email verified)
- last_login_at
- settings (JSON) - theme, notifications
```

#### `clients` - ADD Columns
```
ADD:
- workspace_id (FK) - tenant isolation
- contact_person
- tax_id
- billing_address
- status (active/inactive/archived)
- INDEX(workspace_id, id)
```

#### `products` - ADD Columns
```
ADD:
- workspace_id (FK)
- description
- sku
- stock_level
- category
- margin (profit percentage)
- status (active/inactive/discontinued)
- INDEX(workspace_id, id)
```

#### `invoices` - ADD Columns
```
MODIFY: client_id -> NOW WITH workspace_id validation
ADD:
- workspace_id (FK)
- invoice_number (unique per workspace)
- status (draft/sent/paid/overdue/cancelled)
- due_date
- payment_method (credit_card/bank_transfer/cash)
- payment_received_at
- notes
- terms
- INDEX(workspace_id, id)
```

#### `invoice_items` - No changes needed
```
Keep as-is (inherits workspace via invoice)
```

---

## 🔗 4. MODEL RELATIONSHIPS

### User Model
```php
// User.php
User::hasMany(Workspace::class, 'user_id')          // Owns workspaces
User::belongsToMany(Workspace::class, 'workspace_users') // Member of workspaces
User::hasMany(WorkspaceUser::class)                  // Membership details
```

### Workspace Model
```php
// Workspace.php
Workspace::belongsTo(User::class)                    // Owner
Workspace::belongsToMany(User::class, 'workspace_users')
Workspace::hasMany(Client::class)
Workspace::hasMany(Product::class)
Workspace::hasMany(Invoice::class)
Workspace::hasMany(AiReport::class)
Workspace::hasOne(Subscription::class)
Subscription::belongsTo(Plan::class)
```

### Subscription Model
```php
// Subscription.php
Subscription::belongsTo(Workspace::class)
Subscription::belongsTo(Plan::class)

// Plan.php
Plan::hasMany(Subscription::class)
```

### Business Entities (Client, Product, Invoice)
```php
// All add workspace relationship
Client::belongsTo(Workspace::class)
Product::belongsTo(Workspace::class)
Invoice::belongsTo(Workspace::class)

// Invoice relationships
Invoice::belongsTo(Client::class)
Invoice::hasMany(InvoiceItem::class)
InvoiceItem::belongsTo(Invoice::class)
InvoiceItem::belongsTo(Product::class)
```

### AI Reports
```php
// AiReport.php
AiReport::belongsTo(Workspace::class)
AiReport::belongsTo(User::class, 'generated_by')
```

---

## 🔐 5. MULTI-TENANCY SECURITY

### Tenant Context Middleware
```php
// WorkspaceMiddleware
- Extract workspace from URL/session
- Verify user access to workspace
- Set app context for automatic scoping
- Prevent cross-workspace data leakage
```

### Query Auto-Scoping (Trait)
```php
// BelongsToWorkspace Trait
- Override all queries with ->where('workspace_id', currentWorkspaceId())
- Prevent accidental unscoped queries
```

### Route Grouping
```php
Route::middleware(['auth', 'workspace'])->group(function () {
    // All routes here auto-filtered by workspace
});
```

---

## 🤖 6. AI FEATURES ARCHITECTURE

### 1. Sales Reports (OpenAI/Claude API)
```
Input: Invoice data from date range + Product mix
Processing:
- Top performing products
- Client analysis
- Revenue trends
- Seasonal patterns
Output: JSON report cached in ai_reports table
```

### 2. Business Dashboard Insights
```
Real-time metrics:
- Total revenue (MTD, YTD)
- Average order value
- Client acquisition rate
- Outstanding invoices
- Top clients by spend

AI Enhancement:
- Trend predictions
- Anomaly detection
- Growth recommendations
```

### 3. Sales Improvement Suggestions
```
Analyze:
- Product performance
- Client payment patterns
- Invoice aging
- Margin analysis
Output: Actionable recommendations
```

### Implementation: Queue + Caching
```
- Triggered by cron or user action
- Queued job calls AI service
- Results cached for 7 days
- User notified when ready
```

---

## 📊 7. ADMIN VS SAAS DASHBOARD

### Admin Dashboard (Super Admin Only)
- Global user management
- Workspace overview
- Subscription analytics
- Revenue/churn metrics
- Plan management
- Audit logs

### SaaS Dashboard (Workspace Members)
- Workspace-specific data
- Invoices, clients, products
- Team management (invite users)
- Subscription settings
- AI reports & insights
- API keys (Pro plan)

---

## 🔄 8. IMPLEMENTATION ROADMAP (MVP FIRST)

### **Phase 1: Foundation (Week 1-2) - MVP**
- [ ] Create Workspace model + migration
- [ ] Create Subscription + Plan models + migrations
- [ ] Add workspace_id to existing tables
- [ ] Create WorkspaceUser pivot table
- [ ] Implement WorkspaceMiddleware
- [ ] Create BelongsToWorkspace trait
- [ ] Update all existing models with workspace relationships
- [ ] Implement workspace routes (group with middleware)
- [ ] Create WorkspaceController (CRUD)
- [ ] Update authentication to set workspace context
- [ ] Create subscription seeder (Free/Basic/Pro plans)

### **Phase 2: Subscriptions & Billing (Week 3)**
- [ ] SubscriptionService (plan switching, limits)
- [ ] SubscriptionMiddleware (enforce plan limits)
- [ ] Integration: Stripe (or Paddle) webhook handlers
- [ ] Billing dashboard view
- [ ] Subscription upgrade/downgrade flows
- [ ] Plan feature matrix validation

### **Phase 3: Team & RBAC (Week 4)**
- [ ] Create Enums: UserRole, SubscriptionPlan
- [ ] WorkspacePolicy authorization
- [ ] Team invitation system
- [ ] Role-based route guards
- [ ] Team management views

### **Phase 4: AI Features (Week 5-6)**
- [ ] Create AiReport model + migration
- [ ] AiService (integration with OpenAI/Claude)
- [ ] Implement 3 report types (sales, products, clients)
- [ ] Queue jobs for async report generation
- [ ] Report generation views
- [ ] Dashboard insights display

### **Phase 5: Admin Panel (Week 7)**
- [ ] Separate admin routes
- [ ] AdminMiddleware
- [ ] Admin dashboard views
- [ ] Global analytics

### **Phase 6: API & Polish (Week 8+)**
- [ ] API authentication (Sanctum tokens)
- [ ] RESTful endpoints
- [ ] API documentation
- [ ] Rate limiting by plan
- [ ] Testing & optimization

---

## 💾 9. DATABASE MIGRATION STRATEGY

### Order of Creation
1. Create `plans` (seed data)
2. Modify `users` table
3. Create `workspaces`
4. Create `workspace_users`
5. Create `subscriptions`
6. Modify `clients`, `products`, `invoices`
7. Create `ai_reports`
8. Create `audit_logs`

### Data Migration (Existing Customers)
```
For each existing user:
  1. Create workspace named "{user.name}'s Workspace"
  2. Migrate all their clients → workspace_id
  3. Migrate all their products → workspace_id
  4. Migrate all their invoices → workspace_id
  5. Create subscription with Free plan
  6. Add user to workspace_users with 'owner' role
```

---

## 🎯 10. CONFIGURATION & CONSTANTS

### `config/saas.php`
```php
return [
    'plans' => [
        'free' => [
            'price' => 0,
            'max_workspaces' => 1,
            'max_clients' => 10,
            'ai_features' => false,
        ],
        'basic' => [
            'price' => 2999, // $29.99/month in cents
            'max_workspaces' => 3,
            'max_clients' => 100,
            'ai_features' => false,
        ],
        'pro' => [
            'price' => 9999,
            'max_workspaces' => null, // unlimited
            'max_clients' => null,
            'ai_features' => true,
        ],
    ],
    'stripe' => ['key' => env('STRIPE_PUBLIC_KEY'), ...],
];
```

---

## 🔒 11. SECURITY CONSIDERATIONS

1. **Workspace Isolation**: Always filter by workspace_id
2. **Rate Limiting**: By workspace + plan tier
3. **API Rate Limits**: Free:100/day, Basic:1000/day, Pro:unlimited
4. **Audit Logging**: Track all data changes with IP + user
5. **Encryption**: Sensitive data (tax IDs, etc.)
6. **CORS/CSRF**: Standard Laravel protections
7. **Permission Caching**: Cache role checks (5min TTL)

---

## 📈 12. SCALABILITY ROADMAP

- **Database**: Add indices on workspace_id columns
- **Caching**: Redis for workspace context + role cache
- **Queues**: Async report generation + email notifications
- **API**: Separate API servers (future load balancing)
- **CDN**: Static assets + PDF invoices
- **Search**: Elasticsearch for invoice/client search (Pro only)

---

## ✅ NEXT STEPS

1. **Review this document** with your team
2. **Start Phase 1** (Foundation)
3. **Create migrations** (in separate document)
4. **Update models** (step-by-step)
5. **Test thoroughly** before Phase 2

---

**Created**: 2026-01-24  
**Document Version**: 1.0  
**Status**: Ready for Implementation
