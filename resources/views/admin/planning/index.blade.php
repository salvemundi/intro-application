@extends('layouts.app')
@section('content')

<script>
    setActive("events");
</script>

<div>
    @include('admin.planning.modals.categories', ['categories' => $categories, 'shiftLeaders' => $shiftLeaders])
    @include('admin.planning.modals.shifts', ['shifts' => $shifts, 'categories' => $categories, 'shiftLeaders' => $shiftLeaders])
    @include('admin.planning.modals.shiftsParticipants', ['shifts' => $shifts, 'parents' => $parents])

    <div class="row">
        <div class="col-12 mb-5 container">
            <div class="btn-group mb-2 " role="group" aria-label="Basic outlined example">
                <button type="button" data-bs-toggle="modal" data-bs-target="#CategoryModal" class="btn btn-outline-primary"><i class="fas fa-layer-group"></i> Categorieën</button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#ShiftsModal" class="btn btn-outline-primary"><i class="fas fa-briefcase"></i> Diensten</button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#ShiftsParticipantsModal" class="btn btn-outline-primary"><i class="fas fa-link"></i> Diensten koppelen</button>
            </div>
            <form method="get" action="/admin/planning" class="d-flex align-items-center ">
                @csrf
                <select class="form-select" id="multiple-select-field" data-placeholder="Choose anything" multiple name="shiftsRequested[]">
                    @foreach($parents as $parent)
                        @if($requestedParticipants->contains($parent->id))
                            <option value="{{$parent->id}}" selected>{{ $parent->displayName() }}</option>
                        @else
                            <option value="{{$parent->id}}">{{ $parent->displayName() }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary ms-4 me-2">Zoek</button>
            </form>
            @include('admin.planning.calendar', ['shifts' => $shifts])
        </div>
    </div>
</div>
<script>
    $( '#multiple-select-field' ).select2( {
        theme: "bootstrap-5",
        width: '100%',
        placeholder: $( this ).data( 'placeholder' ),
        closeOnSelect: false,
    } );
</script>
@endsection
