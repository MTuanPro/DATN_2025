<?php

namespace App\Console\Commands;

use App\Services\ChatbotLearningService;
use Illuminate\Console\Command;

class ChatbotAnalyzeFeedback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:analyze-feedback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze chatbot feedback and adjust knowledge base priorities';

    /**
     * Execute the console command.
     */
    public function handle(ChatbotLearningService $learningService)
    {
        $this->info('Starting chatbot feedback analysis...');

        $stats = $learningService->analyzeFeedback();

        $this->info('Feedback analysis completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Positive Feedback Processed', $stats['processed_positive']],
                ['Negative Feedback Processed', $stats['processed_negative']],
                ['Knowledge Adjusted', $stats['adjusted_knowledge']],
                ['Errors', $stats['errors']],
            ]
        );

        // Auto-disable low performance knowledge
        $disabled = $learningService->autoDisableLowPerformance();
        if ($disabled > 0) {
            $this->warn("Auto-disabled {$disabled} low-performance knowledge base entries.");
        }

        return Command::SUCCESS;
    }
}

