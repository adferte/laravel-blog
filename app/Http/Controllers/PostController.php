<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    const POST_CACHE_DURATION = 600; // seconds
    const POST_CACHE_KEY = 'posts'; // seconds
    const POSTS_PER_PAGE = 5;

    public function index(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $userPosts = $request->get('mine') ?? 0;
        // Filter by user id ONLY if user is authenticated and parameter mine == 1
        $userPosts = (Auth::check() && $userPosts == 1);

        // Set cache key
        $cacheKey = self::POST_CACHE_KEY;
        if ($userPosts) {
            $cacheKey .= Auth::id();
        }

        // Return cached posts if they exist, else we retrieve them all from database
        $posts = Cache::remember($cacheKey, self::POST_CACHE_DURATION, function () use ($userPosts) {
            $posts = $userPosts ?
                Post::where('user_id', Auth::id()) :
                Post::where('id', '>', 0);
            return $posts->latest()->get();
        });

        // Log to check out which source we use: cache or database
        if (Cache::has($cacheKey)) {
            Log::info('Cache "' . $cacheKey . '" DOES exist, we use cache');
        } else {
            Log::info('Cache "' . $cacheKey . '" DOES NOT exist, we use database');
        }

        // Paginated this way because using cache and Eloquent's paginate() is chaotic
        // This way we cache all posts in one go and then we paginate over those cached data, instead of caching every page individually
        $posts = new LengthAwarePaginator(
            $posts->forPage($page, self::POSTS_PER_PAGE),
            $posts->count(),
            self::POSTS_PER_PAGE,
            $page,
            ['path' => route('posts.index')]
        );

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

        // Clear global and user specific cache
        Cache::forget(self::POST_CACHE_KEY);
        Cache::forget(self::POST_CACHE_KEY . Auth::id());

        return redirect()->route('posts.index');
    }
}
