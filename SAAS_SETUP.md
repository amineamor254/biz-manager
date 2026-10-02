Convert this Laravel project into a SaaS MVP.

Current system:
- Laravel CRUD (Clients, Products, Invoice, Orders)
- Admin dashboard + Auth

GOAL:
Build a working SaaS MVP fast.

RULES:
- Focus ONLY on MVP (no overengineering)
- Use simple multi-tenancy (add workspace_id column to all tables)
- Add subscription system (Free / Pro only)
- Add roles: admin, owner, user
- Keep structure simple (no repositories unless needed)

AI FEATURES (simple version):
- Add service class AIService
- Function: generateSalesSummary($workspaceId)
- Function: suggestImprovements($workspaceId)

DELIVER:
1. migrations updates
2. models relationships
3. middleware for tenant (workspace)
4. basic subscription logic
5. AIService class (mock or API ready)
6. minimal SaaS architecture

IMPORTANT:
Generate code step by step, not explanations.
Start with database migrations only.