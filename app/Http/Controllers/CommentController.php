<?php

namespace App\Http\Controllers;

use App\Enums\Commentables;
use App\Http\Requests\Comment\AddCommentRequest;
use App\Http\Requests\Comment\CommentRequest;
use App\Http\Requests\Comment\EditCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(CommentRequest $request): AnonymousResourceCollection
    {
        $comments = Comment::query();

        if ($request->has('id') && $request->has('type')) {
            $class = Commentables::fromName($request->get('type'))->value;
            /* @var Model $class */
            $commentable = $class::query()->find($request->get('id'));
            if ($commentable) {
                $comments = $commentable->twits();
            }
        }

        $comments->with(['user', 'commentable'])
            ->orderBy('created_at', 'DESC');

        return CommentResource::collection($comments->paginate());
    }

    public function store(AddCommentRequest $request): CommentResource
    {
        $class = Commentables::fromName($request->get('type'))->value;

        $comment = Comment::create([
            'name' => Auth::user()->username,
            'email' => Auth::user()->email,
            'approved' => 1,
            'commentable_id' => $request->get('id'),
            'comment' => $request->get('comment'),
            'commentable_type' => $class,
            'time' => 0,
        ]);
        return new CommentResource($comment);
    }

    public function update(EditCommentRequest $request, Comment $comment): CommentResource
    {
        if ($comment->name === Auth::user()->username || Auth::user()->username === 'andreev') {
            $comment->update($request->validated());
        }
        return new CommentResource($comment);
    }

    public function destroy(Comment $comment): \Illuminate\Http\Response|JsonResponse
    {
        if (Auth::user()->username === 'andreev' || Auth::user()->username === $comment->name) {
            $comment->delete();
        }
        return response()->noContent();
    }

    public function approve(Comment $comment)
    {
        if (Auth::user()->username === 'andreev') {
            $comment->approved = true;
            $comment->save();
        }
        return new CommentResource($comment);
    }
}
