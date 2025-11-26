<?php

namespace App\DTO;

class CreateUserReviewDTO
{
    public function __construct(
        public int $reviewer_id,
        public int $reviewee_id,
        public int $rating,
        public string $comment,
        public ?string $title = null,
        public ?array $tags = null,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            reviewer_id: $data['reviewer_id'],
            reviewee_id: $data['reviewee_id'],
            rating: $data['rating'],
            comment: $data['comment'],
            title: $data['title'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'reviewer_id' => $this->reviewer_id,
            'reviewee_id' => $this->reviewee_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'title' => $this->title,
            'tags' => $this->tags,
            'helpful_count' => 0,
        ];
    }
}
