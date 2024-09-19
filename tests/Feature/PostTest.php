<?php

namespace Tests\Feature;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_posts_can_be_fetched(): void
    {
        $posts = Post::factory()->count(1)->create();

        $response = $this->get(route('posts.index'));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'data' => PostResource::collection($posts)->toArray(request())
            ]);
    }

    public function test_single_post_can_be_fetched(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', ['post' => $post]));

        $response
            ->assertOk()
            ->assertJsonFragment((new PostResource($post))->toArray(request()));
    }

    public function test_post_can_be_stored(): void
    {
        User::factory()->create();

        $response = $this->post(route('posts.store'), ['body' => 'test body']);

        $response->assertCreated();

        $this->assertDatabaseHas(Post::class, [
            'body' => 'test body'
        ]);
    }

    public function test_post_can_be_updated(): void
    {
        $post = Post::factory()->create();

        $response = $this->put(route('posts.show', ['post' => $post]), ['body' => 'test body']);

        $response->assertOk();

        $this->assertDatabaseHas(Post::class, [
            'body' => 'test body'
        ]);
    }

    public function test_post_can_be_deleted(): void
    {
        $post = Post::factory()->create();

        $response = $this->delete(route('posts.destroy', ['post' => $post]));

        $response->assertNoContent();

        $this->assertDatabaseMissing($post);
    }
}
