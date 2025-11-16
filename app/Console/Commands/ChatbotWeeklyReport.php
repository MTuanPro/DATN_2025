<?php

namespace App\Console\Commands;

use App\Services\ChatbotLearningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ChatbotWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:weekly-report {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send weekly chatbot performance report';

    /**
     * Execute the console command.
     */
    public function handle(ChatbotLearningService $learningService)
    {
        $this->info('Generating weekly chatbot report...');

        $report = $learningService->generateWeeklyReport();

        // Display accuracy stats
        $this->info("\n=== ACCURACY STATS (Last 7 days) ===");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Responses', $report['accuracy']['total_responses']],
                ['With Feedback', $report['accuracy']['with_feedback']],
                ['Feedback Rate', $report['accuracy']['feedback_rate'] . '%'],
                ['Positive Feedback', $report['accuracy']['positive_feedback']],
                ['Negative Feedback', $report['accuracy']['negative_feedback']],
                ['Accuracy Rate', $report['accuracy']['accuracy_rate'] . '%'],
            ]
        );

        // Display unanswered questions
        $this->info("\n=== SUGGESTED NEW KNOWLEDGE (Top 10) ===");
        if ($report['suggestions']->isEmpty()) {
            $this->info('No suggestions. Bot is performing well!');
        } else {
            $suggestions = $report['suggestions']->take(10)->map(function($item) {
                return [
                    'question' => \Illuminate\Support\Str::limit($item->noi_dung, 60),
                    'occurrences' => $item->occurrences,
                ];
            })->toArray();
            
            $this->table(['Question', 'Asked Count'], $suggestions);
        }

        // Display low performance knowledge
        $this->info("\n=== LOW PERFORMANCE KNOWLEDGE ===");
        if ($report['low_performance']->isEmpty()) {
            $this->info('All knowledge bases are performing well!');
        } else {
            $lowPerf = $report['low_performance']->take(10)->map(function($item) {
                return [
                    'ID' => $item->id,
                    'Topic' => $item->chu_de,
                    'Question' => \Illuminate\Support\Str::limit($item->cau_hoi_mau, 40),
                    'Usage' => $item->total_usage,
                    'Negative' => $item->negative_feedback_count,
                    'Rate' => round(($item->negative_feedback_count / $item->total_usage) * 100) . '%',
                ];
            })->toArray();
            
            $this->table(
                ['ID', 'Topic', 'Question', 'Usage', 'Negative', 'Rate'],
                $lowPerf
            );
        }

        // Log report
        Log::info('Chatbot Weekly Report Generated', [
            'accuracy_rate' => $report['accuracy']['accuracy_rate'],
            'suggestions_count' => $report['suggestions']->count(),
            'low_performance_count' => $report['low_performance']->count(),
        ]);

        // TODO: Send email to admin
        // $email = $this->option('email') ?? config('chatbot.admin_email');
        // if ($email) {
        //     Mail::to($email)->send(new ChatbotWeeklyReportMail($report));
        //     $this->info("\nReport sent to: {$email}");
        // }

        $this->info("\n✅ Weekly report generated successfully!");

        return Command::SUCCESS;
    }
}

