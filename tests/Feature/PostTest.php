<?php

namespace Tests\Feature;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostTest extends TestCase
{
    public $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    /**
     * GET: get top posts
     */
    public function test_top_posts_by_comment_count()
    {
        $response = $this->json('GET', '/api/posts');
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has(
                "data", fn ($json) =>
                    $json->has("post_id")
                        ->has('post_title')
                        ->has('post_body')
                        ->has('total_number_of_comments')
                        ->etc()
                )
                ->etc()
        );
    }

    /**
     * POST: post store
     */
    public function test_store_post()
    {
        $response = $this->json('POST', '/api/posts', [
            'title' => $this->faker->sentence,
            'body' => $this->faker->sentence
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has("data", fn ($json) => $json->has("post")->etc())
                ->etc()
        );
    }
}
