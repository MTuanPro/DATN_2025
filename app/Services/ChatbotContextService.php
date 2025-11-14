<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ChatbotContextService
{
    /**
     * Prefix cho cache key
     */
    const CACHE_PREFIX = 'chatbot_context:';

    /**
     * TTL cho context (1 giờ)
     */
    const CACHE_TTL = 3600;

    /**
     * Max số topics lưu trong history
     */
    const MAX_TOPIC_HISTORY = 5;

    /**
     * Lưu context cho conversation
     * 
     * @param int $conversationId
     * @param array $context
     * @return void
     */
    public function saveContext(int $conversationId, array $context): void
    {
        $cacheKey = $this->getCacheKey($conversationId);
        Cache::put($cacheKey, $context, self::CACHE_TTL);
    }

    /**
     * Lấy context của conversation
     * 
     * @param int $conversationId
     * @return array
     */
    public function getContext(int $conversationId): array
    {
        $cacheKey = $this->getCacheKey($conversationId);
        return Cache::get($cacheKey, $this->getDefaultContext());
    }

    /**
     * Update context sau mỗi message
     * 
     * @param int $conversationId
     * @param array $newData
     * @return void
     */
    public function updateContext(int $conversationId, array $newData): void
    {
        $context = $this->getContext($conversationId);

        // Merge new data
        $context = array_merge($context, $newData);

        // Manage topic history (keep only last N topics)
        if (isset($context['topics']) && is_array($context['topics'])) {
            $context['topics'] = array_slice($context['topics'], -self::MAX_TOPIC_HISTORY);
            $context['topics'] = array_values(array_unique($context['topics'])); // Remove duplicates
        }

        // Manage entity history (keep only relevant entities)
        if (isset($context['entity_history']) && is_array($context['entity_history'])) {
            $context['entity_history'] = array_slice($context['entity_history'], -10);
        }

        // Update timestamp
        $context['last_updated'] = now()->toDateTimeString();

        $this->saveContext($conversationId, $context);
    }

    /**
     * Add topic to history
     * 
     * @param int $conversationId
     * @param string $topic
     * @return void
     */
    public function addTopic(int $conversationId, string $topic): void
    {
        $context = $this->getContext($conversationId);
        
        if (!isset($context['topics'])) {
            $context['topics'] = [];
        }

        $context['topics'][] = $topic;
        $context['previous_topic'] = $topic;

        $this->updateContext($conversationId, $context);
    }

    /**
     * Add entities to context
     * 
     * @param int $conversationId
     * @param array $entities
     * @return void
     */
    public function addEntities(int $conversationId, array $entities): void
    {
        $context = $this->getContext($conversationId);

        // Update current entities
        $context['entities'] = array_merge($context['entities'] ?? [], $entities);

        // Add to entity history for tracking
        if (!isset($context['entity_history'])) {
            $context['entity_history'] = [];
        }

        $context['entity_history'][] = [
            'entities' => $entities,
            'timestamp' => now()->toDateTimeString(),
        ];

        $this->updateContext($conversationId, $context);
    }

    /**
     * Set last question and response
     * 
     * @param int $conversationId
     * @param string $question
     * @param string $response
     * @param float $similarity
     * @return void
     */
    public function setLastInteraction(int $conversationId, string $question, string $response, float $similarity): void
    {
        $this->updateContext($conversationId, [
            'last_question' => $question,
            'last_response' => $response,
            'last_similarity' => $similarity,
        ]);
    }

    /**
     * Get previous topic
     * 
     * @param int $conversationId
     * @return string|null
     */
    public function getPreviousTopic(int $conversationId): ?string
    {
        $context = $this->getContext($conversationId);
        return $context['previous_topic'] ?? null;
    }

    /**
     * Get current entities
     * 
     * @param int $conversationId
     * @return array
     */
    public function getEntities(int $conversationId): array
    {
        $context = $this->getContext($conversationId);
        return $context['entities'] ?? [];
    }

    /**
     * Clear context cho conversation (khi đóng hoặc reset)
     * 
     * @param int $conversationId
     * @return void
     */
    public function clearContext(int $conversationId): void
    {
        $cacheKey = $this->getCacheKey($conversationId);
        Cache::forget($cacheKey);
    }

    /**
     * Check if conversation has context
     * 
     * @param int $conversationId
     * @return bool
     */
    public function hasContext(int $conversationId): bool
    {
        $cacheKey = $this->getCacheKey($conversationId);
        return Cache::has($cacheKey);
    }

    /**
     * Extend context TTL (khi user vẫn đang active)
     * 
     * @param int $conversationId
     * @return void
     */
    public function extendContext(int $conversationId): void
    {
        $context = $this->getContext($conversationId);
        if (!empty($context)) {
            $this->saveContext($conversationId, $context);
        }
    }

    /**
     * Get cache key for conversation
     * 
     * @param int $conversationId
     * @return string
     */
    protected function getCacheKey(int $conversationId): string
    {
        return self::CACHE_PREFIX . $conversationId;
    }

    /**
     * Get default context structure
     * 
     * @return array
     */
    protected function getDefaultContext(): array
    {
        return [
            'topics' => [],
            'entities' => [],
            'entity_history' => [],
            'previous_topic' => null,
            'last_question' => null,
            'last_response' => null,
            'last_similarity' => 0,
            'last_updated' => now()->toDateTimeString(),
        ];
    }
}

