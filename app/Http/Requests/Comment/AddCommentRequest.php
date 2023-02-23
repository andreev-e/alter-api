<?php

namespace App\Http\Requests\Comment;

use App\Enums\Commentables;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'comment' => ['required', 'string'],
            'email' => ['sometimes', 'email'],
            'type' => ['required', 'in:' . Commentables::list()],
        ];
    }
}
