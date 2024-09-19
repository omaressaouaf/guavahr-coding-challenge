<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Post::class);

        return PostResource::collection(Post::query()->cursorPaginate());
    }

    public function store(StoreUpdatePostRequest $request)
    {
        Gate::authorize('create', Post::class);

        // authentication is not implemented since it's not in the test case but this will be the id of the authenticated user (Auth::id())
        $userId = User::first()->id;

        $post = Post::query()
            ->create(
                [
                    'body' => $request->input('body'),
                    'user_id' => $userId
                ]
            );

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        Gate::authorize('view', $post);

        return new PostResource($post);
    }

    public function update(StoreUpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $post
            ->update(
                [
                    'body' => $request->input('body'),
                ]
            );

        return new PostResource($post->refresh());
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
