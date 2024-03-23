<?php
// app/Services/CommentService.php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CommentService
{
    public function getCommentsByStatusAndRole($status, $role) : Collection
    {
        return Comment::whereHas($role)
            ->whereIn('status', (array)$status)
            ->get();
    }

    public function updateCommentStatus(Comment $comment, $status, $role) : string
    {
        if ($comment->user->role !== $role) {
            throw ValidationException::withMessages([
                'status' => 'هذا التعليق ليس ل' . __('main.' . $role),
            ]);
        }

        $comment->update([
            'status' => $status,
            $this->getStatusTimestampField($status) => now(),
        ]);

        $statusName = $this->getStatusTimestampField($status);

        return "تم " . ($statusName == 'approved_at' ? 'الموافقة' : ($statusName == 'rejected_at' ? 'رفض' : 'حذف')) . ' التعليق بنجاح';
    }

    private function getStatusTimestampField($status) : ?string
    {
        $statusFields = [
            1 => 'approved_at',
            2 => 'rejected_at',
            3 => 'removed_at',
        ];

        return $statusFields[$status] ?? null;
    }

}
