@extends('layouts.app')
@section('content')

<script>
    setActive("events");
</script>

<div>
    @include('admin.planning.modals.categories', ['categories' => $categories, 'shiftLeaders' => $shiftLeaders])
    <div class="row">
        <div class="col-12 mb-5 container">
            <div class="btn-group mb-2 " role="group" aria-label="Basic outlined example">
                <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-outline-primary"><i class="fas fa-layer-group"></i> Categorieën</button>
                <button type="button" class="btn btn-outline-primary"><i class="fas fa-briefcase"></i> Diensten</button>
            </div>
            <form method="get" action="/admin/planning" class="d-flex align-items-center ">
                @csrf
                <select class="form-select" id="multiple-select-field" data-placeholder="Choose anything" multiple>
                    <option selected>Algemeen</option>
                    @foreach($parents as $parent)
                        <option>{{ $parent->displayName() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary ms-4 me-2">Zoek</button>
            </form>
            @include('admin.planning.calendar')
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
