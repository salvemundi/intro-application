@extends('layouts.app')
@section('content')

    <script>
        setActive("events");
    </script>

<div class="row">
    <div class="col-12 col-lg-6 container">
        @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        @endif
        @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
        @endif

        <div class="table-responsive">
            <div class="d-flex align-items-start">
                <h1 class="display-5 flex-1">Schema</h1>
                <a class="btn btn-primary align-self-center ms-5" href="/admin/planning">Diensten</a>
            </div>
            <table id="table" data-toggle="table" data-search="true" data-sortable="true" data-pagination="true"
            data-show-columns="true">
                <thead>
                    <tr class="tr-class-1">
                        <th data-field="name" data-sortable="true">Naam</th>
                        <th data-field="day" data-sortable="true">Dag</th>
                        <th data-field="time" data-sortable="true">Tijden</th>
                        <th data-field="options" data-sortable="true">Opties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr id="tr-id-3" class="tr-class-2" data-title="bootstrap table">
                            <td data-value="{{ $event->name }}">{{ $event->name }}</td>
                            <td>{{date('l', strtotime($event->beginTime)) }}
                            <td>{{ date('H:i', strtotime($event->beginTime)) }} - {{ date('H:i', strtotime($event->endTime)) }}</td>
                            <td data-value="{{ $event->id }}">
                                <a class="btn btn-primary" href="/events/save/{{ $event->id }}">
                                    Bewerken
                                </a>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $event->id }}">
                                    Verwijderen
                                </button>
                            </td>
                        </tr>
                        <div class="modal fade" id="deleteModal{{ $event->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Verwijder event</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  Weet je zeker dat je {{ $event->name }} wil verwijderen?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nee</button>
                                  <a href="/events/delete/{{ $event->id }}" class="btn btn-danger">Ja</a>
                                </div>
                              </div>
                            </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a class="my-3 mb-5 btn btn-primary" href="events/save" style="width: 100%;">Toevoegen</a>
    </div>
</div>


@endsection
