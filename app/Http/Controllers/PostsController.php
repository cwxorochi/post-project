<?php

namespace App\Http\Controllers;

use App\Events\NewPostEvent;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->responseSuccess(
            Post::with('comments.user')
                ->withCount('comments')
                ->orderByDesc('comments_count')
                ->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $post = null;
            DB::beginTransaction();

            $post = Post::create([
                'post_title' => $request->get('title'),
                'post_body' => $request->get('body'),
                'user_id' => User::inRandomOrder()->first()->id,
            ]);

            broadcast(new NewPostEvent($post->load('comments.user')));

            DB::commit();

            return $this->responseSuccess([
                'post' => $post->load('comments.user')
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return $this->responseFailed();
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->responseSuccess(
            Post::with('comments.user', 'user')->find($id)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = Post::find($id);
            $post->post_title = $request->get('title');
            $post->post_body = $request->get('body');
            $post->save();

            DB::commit();

            return $this->responseSuccess([
                'post' => $post
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return $this->responseFailed();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
