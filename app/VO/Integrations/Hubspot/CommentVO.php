<?php

declare(strict_types=1);

namespace App\VO\Channels\VK\Post;

use Carbon\Carbon;

class CommentVO
{
    /** @var Carbon $date */
    private $published_at;

    /** @var string $message */
    private $message;

    /** @var array $attachments */
    private $attachments;

    /**
     * CommentVO constructor.
     *
     * @param Carbon $published_at
     * @param string $message
     * @param array $attachments
     */
    protected function __construct(Carbon $published_at, string $message, array $attachments)
    {
        $this->message = $message;
        $this->published_at = $published_at;
        $this->attachments = $attachments;
    }

    /**
     * @param PublishedAtVO $publishedAt
     * @param string $message
     * @param array $attachments
     *
     * @return CommentVO
     */
    public static function create(PublishedAtVO $publishedAt, string $message, array $attachments): CommentVO
    {
        return new self($publishedAt->date(), $message, $attachments);
    }

    /**
     * @return Carbon
     */
    public function publishedAt(): Carbon
    {
        return $this->published_at;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function attachments(): array
    {
        return $this->attachments;
    }
}
