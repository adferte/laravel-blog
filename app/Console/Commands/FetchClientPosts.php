<?php

namespace App\Console\Commands;

use App\Post;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchClientPosts extends Command
{
    const POST_EXPECTED_FIELDS = [
        'title' => 'title',
        'body' => 'description',
        'created_at' => 'publication_date',
        'updated_at' => 'publication_date',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-client-posts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch new posts from client API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('email', config('app.admin_email'))->get();

        if ($user->count() === 1) {
            $user = $user->first();
            $client = new Client(['verify' => false]);

            try {
                // Fetch client posts
                $response = $client->request('GET', config('app.post_api_url'));

                if ($response && $response->getStatusCode() == 200) {
                    $postsJson = json_decode($response->getBody()->getContents());

                    // Check invalid JSON
                    if (json_last_error() === 0) {
                        $posts = $postsJson->data;
                        dump($posts);
                        $formatError = false;
                        $postsToSave = [];

                        foreach ($posts as $post) {

                            $p = new Post();
                            $p->user()->associate($user);

                            foreach (self::POST_EXPECTED_FIELDS as $property => $jsonProperty) {
                                // Check JSON has all expected fields, or set error to true and stock check if it hasn't
                                if (!property_exists ($post, $jsonProperty)) {
                                    $formatError = true;
                                    break;
                                }
                                $p->$property = $post->$jsonProperty;
                            }

                            // If there was an error with properties in at least one post, we discard the entire batch and exit the process for consistency sake
                            if ($formatError) {
                                break;
                            }
                            $postsToSave[] = $p;
                        }

                        // If there was no error we proceed with the database insertion
                        if (!$formatError) {
                            foreach ($postsToSave as $post) {
                                $post->save();
                            }
                        }

                    }
                }
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
        }
    }
}
