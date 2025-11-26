<?php

namespace App\DTO;

class CreateServiceReviewDTO
{
    public function __construct(
        public int $reviewer_id,
        public int $service_id,
        public int $rating,
        public string $comment,
        public ?int $order_id = null,
        public ?string $title = null,
        public ?array $tags = null,
        public bool $is_verified_purchase = false,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            reviewer_id: $data['reviewer_id'],
            service_id: $data['service_id'],
            rating: $data['rating'],
            comment: $data['comment'],
            order_id: $data['order_id'] ?? null,
            title: $data['title'] ?? null,
            tags: $data['tags'] ?? null,
            is_verified_purchase: $data['is_verified_purchase'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'reviewer_id' => $this->reviewer_id,
            'service_id' => $this->service_id,
            'order_id' => $this->order_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'title' => $this->title,
            'tags' => $this->tags,
            'is_verified_purchase' => $this->is_verified_purchase,
            'helpful_count' => 0,
        ];
    }
}
