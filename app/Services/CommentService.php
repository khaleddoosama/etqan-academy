<?php
// app/Services/CommentService.php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CommentService
{
    public function getAllComments(): Collection
    {
        return Comment::with(['user', 'course', 'replies'])->latest()->get();
    }

    public function getCommentById(int $id): ?Comment
    {
        return Comment::with(['user', 'course', 'replies'])->find($id);
    }

    public function createComment(array $data): Comment
    {
        $data['user_id'] = auth()->id();
        return Comment::create($data);
    }

    public function updateComment(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment;
    }

    public function deleteComment(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function changeStatus(Comment $comment, int $status, ?string $notes = null): Comment
    {
        $comment->update([
            'status' => $status,
            'notes' => $notes,
            $this->getStatusTimestampField($status) => now(),
        ]);
        return $comment;
    }

    private function getStatusTimestampField($status): ?string
    {
        $statusFields = [
            1 => 'approved_at',
            2 => 'rejected_at',
            3 => 'removed_at',
        ];

        return $statusFields[$status] ?? null;
    }
}
