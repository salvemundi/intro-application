@extends('layouts.app')
@section('content')
<script>
    setActive("events");
</script>
<div class="container center">
    <div id="contact" class="col-md-6">
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
        <form action="/events/save" method="post" enctype="multipart/form-data">
            @csrf
            <br>
            <h2 class="h2">Event opslaan</h2>
            <input type="hidden" name="eventId" id="eventId" value="{{ $event->id ?? null }}">
            <div class="form-group">
                <label for="name">Naam*</label>
                <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ $event->name ?? old('name') }}" id="name" name="name" placeholder="Naam...">
            </div><br>

            <div class="form-group">
                <label for="content">Content*</label>
                <textarea class="form-control wrap{{ $errors->has('description') ? ' is-invalid' : '' }}" id="description" name="description" placeholder="Omschrijving...">{{ $event->description ?? old('description') }}</textarea>
            </div><br>

            <div class="form-group">
                <label for="BeginTime">Begin tijd*</label>
                <input type="datetime-local" class="form-control{{ $errors->has('beginTime') ? ' is-invalid' : '' }}"  value="{{ date("Y-m-d\TH:i", strtotime($event->beginTime ?? '')) ?? old('beginTime') }}" id="beginTime" name="beginTime" placeholder="Begin tijd...">
            </div><br>

            <div class="form-group">
                <label for="EindTime">Eind tijd*</label>
                <input type="datetime-local" class="form-control{{ $errors->has('endTime') ? ' is-invalid' : '' }}" value="{{ date("Y-m-d\TH:i", strtotime($event->endTime ?? '')) ?? old('endTime') }}" id="endTime" name="endTime" placeholder="Eind tijd...">
            </div><br>


            <div class="form-group mb-5">
                <br>
                <input class="btn btn-primary" type="submit" value="Opslaan">
            </div>
        </form>
    </div>
</div>
@endsection
