<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\AiReport;
use Carbon\Carbon;

/**
 * AiService - Integration with AI services (OpenAI, Claude, etc.)
 * Generates business insights and reports
 */
class AiService
{
    private string $apiKey;
    private string $model = 'gpt-4-turbo';

    public function __construct()
    {
        $this->apiKey = config('ai.openai_key');
    }

    /**
     * Generate sales report using AI
     */
    public function generateSalesReport(Workspace $workspace, Carbon $startDate, Carbon $endDate): AiReport
    {
        // Gather data
        $invoices = $workspace->invoices()
            ->where('status', 'paid')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $clients = $workspace->clients()->count();
        $totalRevenue = $invoices->sum('total');

        // Prepare prompt
        $prompt = $this->buildSalesReportPrompt([
            'total_revenue' => $totalRevenue,
            'invoice_count' => $invoices->count(),
            'client_count' => $clients,
            'period' => "{$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}",
        ]);

        // Call AI API
        $reportData = $this->callAiApi($prompt);

        // Cache the report
        return AiReport::create([
            'workspace_id' => $workspace->id,
            'generated_by' => auth()->id(),
            'type' => 'sales_summary',
            'title' => 'Sales Report - ' . $startDate->format('M Y'),
            'description' => 'AI-generated sales analysis',
            'report_data' => $reportData,
            'period_start' => $startDate->date,
            'period_end' => $endDate->date,
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Generate top products report
     */
    public function generateTopProductsReport(Workspace $workspace, int $limit = 10): AiReport
    {
        // Aggregate product sales
        $topProducts = $workspace->invoices()
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->selectRaw('products.*, SUM(invoice_items.quantity) as total_qty')
            ->groupBy('products.id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();

        $prompt = "Analyze these top selling products and provide insights: " . $topProducts->toJson();
        $reportData = $this->callAiApi($prompt);

        return AiReport::create([
            'workspace_id' => $workspace->id,
            'generated_by' => auth()->id(),
            'type' => 'top_products',
            'title' => 'Top Products Analysis',
            'report_data' => $reportData,
            'period_start' => now()->subMonths(1),
            'period_end' => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Generate client analysis report
     */
    public function generateClientAnalysisReport(Workspace $workspace): AiReport
    {
        $clients = $workspace->clients()
            ->withCount(['invoices'])
            ->with(['invoices' => fn($q) => $q->sum('total')])
            ->get();

        $prompt = "Analyze client distribution and value: " . $clients->toJson();
        $reportData = $this->callAiApi($prompt);

        return AiReport::create([
            'workspace_id' => $workspace->id,
            'generated_by' => auth()->id(),
            'type' => 'client_analysis',
            'title' => 'Client Analysis Report',
            'report_data' => $reportData,
            'period_start' => now()->subMonths(3),
            'period_end' => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Build sales report prompt
     */
    private function buildSalesReportPrompt(array $data): string
    {
        return <<<PROMPT
Generate a professional sales report based on this business data:
- Total Revenue: \${$data['total_revenue']}
- Number of Invoices: {$data['invoice_count']}
- Active Clients: {$data['client_count']}
- Period: {$data['period']}

Provide:
1. Executive Summary
2. Key Metrics Analysis
3. Growth Trends
4. Recommendations for Improvement

Format as JSON with 'summary', 'metrics', 'trends', and 'recommendations' keys.
PROMPT;
    }

    /**
     * Call OpenAI API
     */
    private function callAiApi(string $prompt): array
    {
        // TODO: Implement actual OpenAI/Claude API call
        // This is a placeholder showing the structure

        // For now, return mock response
        return [
            'summary' => 'Sales analysis generated',
            'data' => [],
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Get cached report or generate new
     */
    public function getOrGenerateReport(Workspace $workspace, string $type, array $params = []): AiReport
    {
        $recent = AiReport::where('workspace_id', $workspace->id)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($recent && !$recent->isExpired()) {
            return $recent;
        }

        // Generate based on type
        return match ($type) {
            'sales_summary' => $this->generateSalesReport(
                $workspace,
                $params['start_date'] ?? now()->subMonths(1),
                $params['end_date'] ?? now()
            ),
            'top_products' => $this->generateTopProductsReport($workspace),
            'client_analysis' => $this->generateClientAnalysisReport($workspace),
            default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
        };
    }
}
