<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Validation\ValidationException;

class PostService
{
    public function getAllPosts()
    {
        return Post::get();
    }

    public function updatePostStatus(Post $post, $status)
    {

        $post->update([
            'status' => $status,
            $this->getStatusTimestampField($status) => now(),
        ]);

        return true;
        // $statusName = $this->getStatusTimestampField($status);

        // return "تم " . ($statusName == 'approved_at' ? 'الموافقة' : ($statusName == 'rejected_at' ? 'رفض' : 'حذف')) . ' المنشور بنجاح';
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
