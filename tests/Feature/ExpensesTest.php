<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExpensesTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_can_be_created(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('expenses-create@example.com');

        $this->actingAs($user)->get(route('expenses.create'))
            ->assertOk()
            ->assertSee('Add Expense');

        $response = $this->actingAs($user)->post(route('expenses.store'), $this->expensePayload());

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'workspace_id' => $workspace->id,
            'description' => 'Office supplies',
            'amount' => 45.5,
            'category' => 'Supplies',
        ]);
        $this->assertSame(
            '2026-09-20',
            Expense::where('description', 'Office supplies')->firstOrFail()->expense_date->toDateString()
        );
    }

    public function test_expense_is_assigned_to_current_workspace_not_request_workspace_id(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('expenses-tenant@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('expenses-other@example.com');

        $this->actingAs($user)->post(route('expenses.store'), $this->expensePayload([
            'workspace_id' => $otherWorkspace->id,
        ]))->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'description' => 'Office supplies',
            'workspace_id' => $workspace->id,
        ]);
        $this->assertDatabaseMissing('expenses', [
            'description' => 'Office supplies',
            'workspace_id' => $otherWorkspace->id,
        ]);
    }

    public function test_expense_list_only_shows_current_workspace_expenses(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('expenses-list@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('expenses-list-other@example.com');
        $this->insertExpense($workspace->id, ['description' => 'Workspace A expense']);
        $this->insertExpense($otherWorkspace->id, ['description' => 'Workspace B expense']);

        $response = $this->actingAs($user)->get(route('expenses.index'));

        $response->assertOk()
            ->assertSee('Workspace A expense')
            ->assertDontSee('Workspace B expense');
    }

    public function test_empty_expense_list_offers_an_add_action(): void
    {
        [$user] = $this->createWorkspaceUser('expenses-empty@example.com');

        $this->actingAs($user)->get(route('expenses.index'))
            ->assertOk()
            ->assertSee('No expenses yet')
            ->assertSee(route('expenses.create'));
    }

    public function test_user_cannot_view_edit_update_or_delete_another_workspaces_expense(): void
    {
        [$user] = $this->createWorkspaceUser('expenses-security@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('expenses-security-other@example.com');
        $expenseId = $this->insertExpense($otherWorkspace->id, ['description' => 'Private expense']);
        $this->actingAs($user);

        $this->get(route('expenses.show', $expenseId))->assertNotFound();
        $this->get(route('expenses.edit', $expenseId))->assertNotFound();
        $this->put(route('expenses.update', $expenseId), $this->expensePayload(['description' => 'Tampered']))->assertNotFound();
        $this->delete(route('expenses.destroy', $expenseId))->assertNotFound();

        $this->assertDatabaseHas('expenses', [
            'id' => $expenseId,
            'description' => 'Private expense',
            'workspace_id' => $otherWorkspace->id,
        ]);
    }

    public function test_expense_can_be_edited(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('expenses-edit@example.com');
        $expenseId = $this->insertExpense($workspace->id);
        $this->actingAs($user);

        $this->get(route('expenses.edit', $expenseId))
            ->assertOk()
            ->assertSee('Edit Expense');

        $this->put(route('expenses.update', $expenseId), $this->expensePayload([
            'description' => 'Updated supplies',
            'amount' => '80.25',
            'category' => 'Other',
        ]))->assertRedirect(route('expenses.show', $expenseId));

        $this->assertDatabaseHas('expenses', [
            'id' => $expenseId,
            'description' => 'Updated supplies',
            'amount' => '80.25',
            'category' => 'Other',
            'workspace_id' => $workspace->id,
        ]);
        $this->get(route('expenses.show', $expenseId))
            ->assertOk()
            ->assertSee('Updated supplies');
    }

    public function test_expense_can_be_deleted(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('expenses-delete@example.com');
        $expenseId = $this->insertExpense($workspace->id);

        $this->actingAs($user)->delete(route('expenses.destroy', $expenseId))
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseMissing('expenses', ['id' => $expenseId]);
    }

    public function test_expense_validation_rejects_invalid_amount_date_and_description(): void
    {
        [$user] = $this->createWorkspaceUser('expenses-validation@example.com');

        $this->actingAs($user)
            ->from(route('expenses.create'))
            ->post(route('expenses.store'), $this->expensePayload([
                'description' => '',
                'amount' => 0,
                'expense_date' => 'not-a-date',
            ]))
            ->assertRedirect(route('expenses.create'))
            ->assertSessionHasErrors(['description', 'amount', 'expense_date']);

        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_dashboard_total_expenses_uses_only_current_workspace_data(): void
    {
        [$user, $workspace] = $this->createWorkspaceUser('expenses-dashboard@example.com');
        [, $otherWorkspace] = $this->createWorkspaceUser('expenses-dashboard-other@example.com');
        $this->insertExpense($workspace->id, ['amount' => '12.75']);
        $this->insertExpense($otherWorkspace->id, ['amount' => '99.99']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('totalExpenses', fn ($total) => (float) $total === 12.75)
            ->assertSee('$12.75');
    }

    private function expensePayload(array $overrides = []): array
    {
        return array_merge([
            'description' => 'Office supplies',
            'amount' => '45.50',
            'category' => 'Supplies',
            'expense_date' => '2026-09-20',
            'notes' => 'Monthly restock',
        ], $overrides);
    }

    private function insertExpense(int $workspaceId, array $overrides = []): int
    {
        return DB::table('expenses')->insertGetId(array_merge([
            'workspace_id' => $workspaceId,
            'description' => 'Office supplies',
            'amount' => '45.50',
            'category' => 'Supplies',
            'expense_date' => '2026-09-20',
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function createWorkspaceUser(string $email): array
    {
        $user = User::factory()->create([
            'email' => $email,
            'email_verified_at' => now(),
        ]);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'name' => $email . ' Business',
            'slug' => str_replace(['@', '.'], '-', $email) . '-' . $user->id,
        ]);
        WorkspaceUser::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'accepted_at' => now(),
        ]);
        $user->update(['current_workspace_id' => $workspace->id]);

        return [$user->fresh(), $workspace];
    }
}