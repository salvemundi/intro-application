@extends('layouts.guapp')
@section('content')
    <div class="container">
        @if(session()->has('message'))
            <div class="alert alert-primary">
                {{ session()->get('message') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif
            <div class="d-flex flex-column align-items-center justify-content-center">
                <a class="btn btn-primary mb-2" href="https://app.powerbi.com/view?r=eyJrIjoiNzIxMDA4MWItZmUxMC00NThjLTlkOGYtMTlmNTBkMjM4MjhkIiwidCI6ImIyZmFhZDVlLTQ1ZDAtNGJiNS1hZTM1LThmNzQ1MjA0YmJlYiIsImMiOjl9">
                    Open in external tab
                    <i class="fas fa-share-square"></i>
                </a>
                <div class="w-100 m-0" style="height: 75vh;">
                    <iframe title="Report Section" width="100%" height="100%" src="https://app.powerbi.com/view?r=eyJrIjoiNzIxMDA4MWItZmUxMC00NThjLTlkOGYtMTlmNTBkMjM4MjhkIiwidCI6ImIyZmFhZDVlLTQ1ZDAtNGJiNS1hZTM1LThmNzQ1MjA0YmJlYiIsImMiOjl9" frameborder="0" allowFullScreen="true"></iframe>--}}
                </div>
            </div>
    </div>
@endsection
