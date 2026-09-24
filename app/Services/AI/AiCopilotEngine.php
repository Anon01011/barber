<?php

namespace App\Services\AI;

use App\Models\Salon;
use App\Services\AiAnalyticsService;

class AiCopilotEngine
{
    protected $aiAnalyticsService;
    protected $reasoningEngine;
    protected $memoryService;

    public function __construct(
        AiAnalyticsService $aiAnalyticsService,
        AiReasoningEngine $reasoningEngine,
        AiMemoryService $memoryService
    ) {
        $this->aiAnalyticsService = $aiAnalyticsService;
        $this->reasoningEngine = $reasoningEngine;
        $this->memoryService = $memoryService;
    }

    /**
     * Process natural language query with memory retention and deep reasoning over real database entities.
     */
    public function askCopilot(Salon $salon, string $query): array
    {
        // 1. Perform database-driven reasoning
        $reasonedResult = $this->reasoningEngine->reasonOverQuery($salon, $query);

        // 2. Remember interaction in Salon AI memory cache
        $this->memoryService->rememberInteraction($salon, $query, $reasonedResult['response']);

        return [
            'type' => 'copilot_reasoning',
            'response' => $reasonedResult['response'],
            'suggested_actions' => $reasonedResult['suggested_actions'] ?? [],
            'memory_count' => count($this->memoryService->getMemoryHistory($salon)),
        ];
    }
}
