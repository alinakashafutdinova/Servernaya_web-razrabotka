<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentModeratedNotification extends Notification
{
    use Queueable;

    public bool $approved;
    public int $articleId;
    public string $articleTitle;
    public string $commentExcerpt;

    public function __construct(bool $approved, int $articleId, string $articleTitle, string $commentExcerpt)
    {
        $this->approved = $approved;
        $this->articleId = $articleId;
        $this->articleTitle = $articleTitle;
        $this->commentExcerpt = $commentExcerpt;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'status' => $this->approved ? 'approved' : 'rejected',
            'article_id' => $this->articleId,
            'article_title' => $this->articleTitle,
            'comment_excerpt' => mb_strimwidth($this->commentExcerpt, 0, 180, '…'),
        ];
    }
}
