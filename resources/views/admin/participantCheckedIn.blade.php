@extends('layouts.app')
@section('content')
<script>
setActive("participants");
</script>
<div class="row">
    <div class="col-12 col-md-6 container">
        <a href="{{ route('export_excel.excel')}}" class="btn btn-primary btn-sm">Export to Excel</a>
        <div class="table-responsive">
            <table id="table" data-toggle="table" data-search="true" data-sortable="true" data-pagination="true"
            data-show-columns="true">
                <thead>
                    <tr class="tr-class-1">
                        <th data-field="id" data-sortable="true">Id</th>
                        <th data-field="firstName" data-sortable="true">Naam</th>
                        <th data-field="role" data-sortable="true">Rol</th>
                        <th data-field="checkedIn" data-sortable="true">checked in</th>
                        <th data-field="data" data-sortable="true">Gegevens</th>
                        <th data-field="createdat" data-sortable="true">Laatste aanpassing</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participants as $participant)
                        <tr id="tr-id-3" class="tr-class-2" data-title="bootstrap table">
                            <td data-value="{{ $participant->firstName }}">{{ $participant->id }}</td>
                            <td data-value="{{ $participant->firstName }}">{{$participant->firstName}} {{ $participant->lastName }}</td>
                            <td data-value="{{ $participant->role }}">{{ \App\Enums\Roles::fromValue($participant->role)->description }}</td>
                            @if($participant->checkedIn == 1)
                            <td data-value="{{ $participant->checkedIn }}">True</td>
                            @else
                            <td data-value="{{ $participant->checkedIn }}">False</td>
                            @endif
                            <td data-value="{{ $participant->id }}"><a href="/participantscheckedin/{{$participant->id}}"><button type="button" class="btn btn-primary">Details</button></a></td>
                            <td data-value="{{ $participant->firstName }}">{{ $participant->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-12 col-md-6 container mb-5">
        @if (\Session::has('message'))
            <div class="alert alert-danger m-1" role="alert">
                {!! \Session::get('message') !!}
            </div>
        @endif
        @isset($selectedParticipant)
            <div class="card">
            @if ($age <= 18)
                <div class="card-body underEightTeen">
            @else
                <div class="card-body aboveEightTeen">
            @endif
                    <h5 class="card-title">{{ $selectedParticipant->firstName}} {{ $selectedParticipant->lastName }}</h5>
                    <span>
                        @if (\Carbon\Carbon::parse($selectedParticipant->birthday)->diff(\Carbon\Carbon::now())->format('%y years') <= 18)<br>
                            <b> Leeftijd:</b> {{ \Carbon\Carbon::parse($selectedParticipant->birthday)->diff(\Carbon\Carbon::now())->format('%y years') }} <br>
                        @else
                            <b class="aboveEightTeen">Leeftijd:</b> {{ \Carbon\Carbon::parse($selectedParticipant->birthday)->diff(\Carbon\Carbon::now())->format('%y years') }} <br>
                        @endif
                        <b>Email:</b> {{ $selectedParticipant->email}}<br>
                        <b>Telefoon nummer:</b> {{ $selectedParticipant->phoneNumber}}<br>
                        @if($selectedParticipant->role == \App\Enums\Roles::child)
                            <b>Naam Ouder:</b> {{ $selectedParticipant->firstNameParent}} {{ $selectedParticipant->lastNameParent}}<br>
                            <b>Adres Ouder:</b> {{ $selectedParticipant->addressParent}}<br>
                            <b>Telefoonnummer ouder:</b> {{ $selectedParticipant->phoneNumberParent}}<br>
                        @endif
                        <b>Allergieën:</b> {{ $selectedParticipant->medicalIssues}}<br>
                        <b>Bijzonderheden:</b> {{ $selectedParticipant->specials}}<br>


                        <div style="display: flex; flex-direction: row;">
                            @if (!$selectedParticipant->checkedIn)
                                <form method="post" action="/participants/{{ $selectedParticipant->id }}/checkIn">
                                    @csrf<input type="hidden" name="userId" value="{{ $selectedParticipant->id }}">
                                <br>
                                    <button type="submit" class="btn btn-primary ">Checkin</button>
                                </form>
                            @else
                                <form method="post" action="/participants/{{ $selectedParticipant->id }}/checkOut">
                                    @csrf
                                    <button type="submit" class="btn btn-danger ">Checkout</button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Verwijder
                              </button>
                        </div>
                    </span>
                </div>
            </div>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Verwijder</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      Weet je zeker dat jij deelnemer {{ $selectedParticipant->firstName. " " .$selectedParticipant->lastName }} wilt verwijderen?
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form method="post" action="/participants/{{ $selectedParticipant->id }}/delete">
                        @csrf
                        <button type="submit" class="btn btn-danger">Verwijder</button>
                    </form>
                    </div>
                  </div>
                </div>
              </div>
        @endisset
    </div>
</div>


@endsection
