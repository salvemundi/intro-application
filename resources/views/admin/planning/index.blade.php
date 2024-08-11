@php
    Request::is('admin/*') ? $string = 'app' : $string = 'guapp';
@endphp
@extends('layouts.' . $string)
@section('content')

<script>
    setActive("events");
</script>

<div>
    <div class="row">
        <div class="col-12 mb-5 container">
            @if(Request::is('admin/*'))
                @include('admin.planning.modals.categories', ['categories' => $categories, 'shiftLeaders' => $shiftLeaders])
                @include('admin.planning.modals.shifts', ['shifts' => $shifts, 'categories' => $categories, 'shiftLeaders' => $shiftLeaders])
                @include('admin.planning.modals.shiftsParticipants', ['shifts' => $shifts, 'parentsAndCrew' => $parentsAndCrew])
                @include('admin.planning.modals.hoursChart', ['parents' => $parents])
            @endif
            @if(Request::is('admin/*'))
                <div class="btn-group mb-2 " role="group" aria-label="Basic outlined example">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#CategoryModal" class="btn btn-outline-primary"><i class="fas fa-layer-group"></i> Categorieën</button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#ShiftsModal" class="btn btn-outline-primary"><i class="fas fa-briefcase"></i> Diensten</button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#ShiftsParticipantsModal" class="btn btn-outline-primary"><i class="fas fa-link"></i> Diensten koppelen</button>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#HoursChartModal" class="btn btn-outline-primary"><i class="fas fa-chart-bar"></i> Diensten vergelijken</button>
                </div>
            @endif
            @if(Request::is('admin/*'))
            <form method="get" action="/admin/planning" class="d-flex align-items-center ">
            @else
            <button class="btn btn-primary mb-2" onclick="getSelected()"><i class="fas fa-link" id="liveToastBtn"></i> Synchroniseer de huidige selectie met je agenda</button>
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100000">
                <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Agenda</strong>
                        <small>Nu</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        De link is gekopieerd naar je klembord!
                    </div>
                </div>
            </div>
            <form method="get" action="/planning" class="d-flex align-items-center ">
            @endif
                <select class="form-select" id="multiple-select-field" data-placeholder="Choose anything" multiple name="shiftsRequested[]">
                    @foreach($categories as $category)
                        @if($requestedParticipants->contains($category->id))
                            <option value="{{$category->id}}" selected>{{ $category->name . " -- Categorie"}}</option>
                        @else
                            <option value="{{$category->id}}">{{ $category->name . " -- Categorie"}}</option>
                        @endif
                    @endforeach
                    @foreach($parentsAndCrew as $parent)
                        @if($requestedParticipants->contains($parent->id))
                            <option value="{{$parent->id}}" selected>{{ $parent->displayName() }}</option>
                        @else
                            <option value="{{$parent->id}}">{{ $parent->displayName() }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary ms-4 me-2">Zoek</button>
            </form>
            @include('admin.planning.calendar', ['shifts' => $shifts,'filteredShifts' => $filteredShifts])
        </div>
    </div>
</div>
<script>
    // get the select element and get the currently selected elements and put them in an array to be concatenated into one string
    var toastTrigger = document.getElementById('liveToastBtn')
    var toastLiveExample = document.getElementById('liveToast')
    if (toastTrigger) {
        toastTrigger.addEventListener('click', function () {
            var toast = new bootstrap.Toast(toastLiveExample)

            toast.show()
        })
    }
    function getSelected() {
        let selected = [];
        let select = document.getElementById('multiple-select-field');
        for (let i = 0; i < select.length; i++) {
            if (select.options[i].selected) {
                selected.push(select.options[i].value);
            }
        }
        // join the array into a string
        let selectedString = selected.join(',');
        const url = "{{Request::root()}}/planning/ical?calendars=" + selectedString;
        // copy the string to the clipboard
        navigator.clipboard.writeText(url).then(function() {
            var toastLiveExample = document.getElementById('liveToast')
            var toast = new bootstrap.Toast(toastLiveExample)

            toast.show()
        }, function(err) {
            console.error('Async: Could not copy text: ', err);
        });
    }

    $( '#multiple-select-field' ).select2( {
        theme: "bootstrap-5",
        width: '100%',
        placeholder: $( this ).data( 'placeholder' ),
        closeOnSelect: false,
    } );
</script>
@endsection
