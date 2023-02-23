<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'comment' => ['required', 'string'],
            'email' => ['sometimes', 'email'],
            'type' => ['required', 'string'],
        ];
    }
}
