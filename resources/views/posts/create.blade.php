@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('posts') }}" enctype="multipart/form-data" method="post">
            @csrf

            <div class="row">
                <div class="col-6 offset-3">

                    <div class="row">
                        <h1>Create Post</h1>
                    </div>
                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label">Title</label>
                        <input id="title" type="text" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" name="title" value="{{ old('title') }}">
                        @if ($errors->has('title'))
                            <span style="color: red" role="alert">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="row">
                        <label for="body" class="col-md-4 col-form-label">Body</label>
                        <textarea rows="10" id="body" class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}" name="body">{{ old('body') }}</textarea>
                        @if ($errors->has('body'))
                            <span style="color: red" role="alert">
                                <strong>{{ $errors->first('body') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="row pt-4">
                        <button class="btn btn-primary">Create</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection
