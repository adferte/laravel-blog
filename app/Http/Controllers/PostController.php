<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function index(Request $request)
    {
        // Filter by user id ONLY if user is authenticated and parameter mine == 1
        $userPosts = $request->get('mine') ?? 0;
        $posts = (Auth::check() && $userPosts == 1) ?
            Post::where('user_id', Auth::id()) :
            Post::where('id', '>', 0);

        $posts = $posts->latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(CreatePostRequest $request)
    {
        // Use custom request to validate parameters
        $requestData = $request->validated();

        // Create post assigned to current user
        $post = new Post();
        $post->user()->associate(Auth::user());
        $post->fill($requestData);
        $post->save();

        return redirect()->route('posts.index');
    }
}
