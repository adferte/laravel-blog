@extends('layouts.app')

@section('content')
    <div class="container">
        @foreach($posts as $post)
            <div style="background-color: white; border-radius: 25px; padding: 10px 20px; margin: 40px 10px; box-shadow: 3px 3px 3px 4px #ccc;">
                <div class="row">
                    <div class="col-12" style="font-size: x-large; padding-top: 10px">{{ $post->title }}</div>
                </div>
                <div class="row">
                    <div class="col-12" style="color: grey; font-size: small; padding-top: 3px; padding-bottom: 20px"><span style="font-weight: bold">{{ $post->user->name }}</span> at {{ $post->created_at }}</div>
                </div>
                <div class="row">
                    <div class="col-12" style="font-size: medium; white-space: pre-line; padding-bottom: 20px; text-align: justify; text-justify: inter-word;">{{ $post->body }}</div>
                </div>
            </div>
        @endforeach
        <div class="row">
            <div class="col-12" style="padding: 10px; margin: 20px;">
                {{ $posts->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
