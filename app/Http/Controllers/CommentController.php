<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCommentRequest;
use App\Models\Comment;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $id = (integer) $request->input('id');
        $type = $request->input('type');

        $comments = Comment::query()
            ->when(!$request->input('pending'), function($query) {
                $query->where('approved', 1);
            })->orderBy('time', 'DESC');
        if ($id && $type === 'poi') {
            $comments->where('backlink', $id);
        }
        $comments->with('user');

        return CommentResource::collection($comments->paginate());
    }

    public function store(AddCommentRequest $request): JsonResponse
    {
        if (Auth::user()) {
            $comment = new Comment([
                'name' => Auth::user()->username,
                'email' => Auth::user()->email,
                'approved' => 1,
            ]);
        } else {
            $comment =  new Comment([
                'name' => '',
                'email' => $request->get('email'),
                'approved' => 0,
            ]);
        }
        $comment->fill([
            'backlink' => $request->get('id'),
            'comment' => $request->get('comment'),
            'time' => Carbon::now()->unix(),
        ])->save();
        return response()->json('Ok');
    }

    public function update(Request $request, Comment $comment)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        if (Auth::user()->username === 'andreev') {
            $comment->delete();
        }
    }
}
