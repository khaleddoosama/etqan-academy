<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use ApiResponseTrait;

    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function show(int $id)
    {
        $comment = $this->commentService->getCommentById($id);
        return $this->apiResponse($comment, __('messages.comment_retrieved_successfully'), 200);
    }

    public function store(StoreCommentRequest $request): Response|ResponseFactory
    {
        $validated = $request->validated();

        $comment = $this->commentService->createComment($validated);
        return $this->apiResponse($comment, __('messages.comment_created_successfully'), 201);
    }

    // public function update(Request $request, Comment $comment): JsonResponse
    // {
    //     $validated = $request->validate([
    //         'content' => 'string',
    //     ]);

    //     $updatedComment = $this->commentService->updateComment($comment, $validated);
    //     return response()->json($updatedComment);
    // }

    public function destroy(Comment $comment): Response|ResponseFactory
    {
        $this->commentService->deleteComment($comment);
        return $this->apiResponse(null, __('messages.comment_deleted_successfully'), 200);
    }
}
