<?php

namespace App\Http\Controllers;

use App\Events\NewCommentEvent;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $builder = Comment::with('user', 'post')->searchPattern($request->get('pattern'));
        if ($request->get('limiter', false)) {
            $builder->take($request->get('limiter'));
        }
        return $this->responseSuccess(
            $builder->orderByDesc('created_at')->get()
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

            $post = Post::find($request->get('post_id'));
            $comment = $post->comments()->create([
                'user_id' => User::inRandomOrder()->first()->id,
                'body' => $request->get('comment')
            ]);

            broadcast(new NewCommentEvent($comment->load('user', 'post')));
            DB::commit();

            return $this->responseSuccess([
                'comment' => $comment->load('user')
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->responseFailed();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
