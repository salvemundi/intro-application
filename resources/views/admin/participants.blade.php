@extends('layouts.app')
@section('content')
<script>
setActive("participants");
</script>
<div class="row">
    <div class="col-12 col-md-6 container">
        <div class="d-flex">
            <a href="{{ route('export_excel.excel')}}" class="btn btn-primary btn-sm">Export to Excel</a>
            <a href="{{ route('studentNumbers.excel')}}" class="btn btn-primary btn-sm" style="margin-left: 4px;">Export student numbers</a>

            <div class="dropdown" style="margin-left: 4px;">
                <button class="btn btn-secondary dropdown-toggle" style="width: auto !important;" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <li><button class="dropdown-item" id="filterByCheckedInOnly" value="NO" type="button">Ingechecked</button></li>
                    <li><button class="dropdown-item" id="filterByRemovedFromTerrain" value="NO" type="button">Definitief weg</button></li>
                    <li><button class="dropdown-item" id="filterByNote" value="NO" type="button">Deelnemers met opmerking</button></li>
                </ul>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#checkoutEveryoneModal">
                Check iedereen uit
            </button>

            <!-- Modal -->
            <div class="modal fade" id="checkoutEveryoneModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Iedereen uitchecken?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Weet je zeker dat je iedereen wil uitchecken?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nah doe toch maar nie</button>
                            <form method="POST" action="/participants/checkOutEveryone">
                                @csrf
                                <button type="submit" class="btn btn-danger">Bevestigen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



        </div>

        <div class="table-responsive">
            <table id="table" data-toggle="table" data-search="true" data-sortable="true" data-pagination="true"
            data-show-columns="true">
                <thead>
                    <tr class="tr-class-1">
                        <th data-field="Id" data-sortable="true">Id</th>
                        <th data-field="firstName" data-sortable="true">Naam</th>
                        <th data-field="role" data-sortable="true">Rol</th>
                        <th data-field="checkedIn" data-sortable="true">checked in</th>
                        <th data-field="data" data-sortable="true">Gegevens</th>
                        <th data-field="createdat" data-sortable="true">Laatste aanpassing</th>
                        <th data-field="note" data-sortable="false">Notitie</th>
                        <th data-field="removed" data-sortable="false">Verwijderd</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participants as $participant)
                        <tr id="tr-id-3" class="tr-class-2" data-title="bootstrap table">
                            <td data-value="{{ $participant->firstName }}">{{ $participant->id }}</td>
                            <td data-value="{{ $participant->firstName }}">{{ $participant->firstName }} {{ $participant->lastName }}</td>
                            <td data-value="{{ $participant->role }}">{{ \App\Enums\Roles::fromValue($participant->role)->description }}</td>
                            @if($participant->checkedIn == 1)
                                <td data-value="{{ $participant->checkedIn }}">True</td>
                            @else
                                <td data-value="{{ $participant->checkedIn }}">False</td>
                            @endif
                            <td data-value="{{ $participant->id }}"><a href="/participants/{{$participant->id}}"><button type="button" class="btn btn-primary">Details</button></a></td>
                            <td data-value="{{ $participant->firstName }}">{{ $participant->updated_at }}</td>
                            <td data-value="{{ $participant->note }}">{{ $participant->note }}</td>
                            @if($participant->removedFromIntro == 1)
                                <td data-value="{{ $participant->removedFromIntro }}">True</td>
                            @else
                                <td data-value="{{ $participant->removedFromIntro }}">False</td>
                            @endif
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
                <div class="card-body underEightTeen d-flex">
            @else
                <div class="card-body aboveEightTeen d-flex">
            @endif
                    <div class="flex-column w-50">
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
                                <b>Leerjaar:</b> {{ App\Enums\StudentYear::fromvalue($selectedParticipant->studentYear)->key}}<br>
                                <b>Naam Ouder:</b> {{ $selectedParticipant->firstNameParent}} {{ $selectedParticipant->lastNameParent}}<br>
                                <b>Adres Ouder:</b> {{ $selectedParticipant->addressParent}}<br>
                                <b>Telefoonnummer ouder:</b> {{ $selectedParticipant->phoneNumberParent}}<br>
                            @endif
                            <b>Allergieën:</b> {{ $selectedParticipant->medicalIssues}}<br>
                            <b>Bijzonderheden:</b> {{ $selectedParticipant->specials}}<br>

                            <div style="display: flex; flex-direction: row;">
                                @if (!$selectedParticipant->checkedIn)
                                    <form method="post" action="/participants/{{ $selectedParticipant->id }}/checkIn">
                                        @csrf
                                            <button type="submit" class="btn btn-primary buttonPart me-2">Checkin</button>
                                    </form>
                                @else
                                    <form method="post" action="/participants/{{ $selectedParticipant->id }}/checkOut">
                                        @csrf
                                        <button type="submit" class="btn btn-danger buttonPart me-2">Checkout</button>
                                    </form>
                                @endif
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Verwijder van database
                                </button>
                            </div>
                        </span>
                    </div>
                    <div class="flex-column w-50">
                        <div>
                            <form class="mb-2" method="POST" action="/participants/{{ $selectedParticipant->id }}/storeNote">
                                @csrf
                                <div class="form-floating">
                                    <textarea class="form-control" name="participantNote" placeholder="Leave a comment here" id="participantNote" style="height: 100px">{{ $selectedParticipant->note }}</textarea>
                                    <label for="participantNote">Opmerkingen over deelnemer</label>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Opslaan</button>
                            </form>
                            <form method="POST" action="/participants/{{ $selectedParticipant->id }}/storeRemove">
                                @csrf
                                @if(!$selectedParticipant->removedFromIntro)
                                    <button type="submit" class="btn btn-danger">Verwijder deelnemer van terrein / intro</button>
                                @else
                                    <button type="submit" class="btn btn-success">Laat deelnemer weer toe op terrein / intro</button>
                                @endif
                            </form>
                        </div>

                    </div>
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
    <script>
        var $table = $('#table')

        $(function() {
            $table.bootstrapTable('hideColumn',['note','removed'])
            $('#filterByCheckedInOnly').click(function () {
                if(this.value === "YES"){
                    this.value="NO";

                    $table.bootstrapTable('filterBy', {
                        // Reset filter
                    })
                }
                else if(this.value === "NO"){
                    this.value="YES";
                    $table.bootstrapTable('filterBy', {
                        checkedIn: "True"
                    })
                }

            })
            $('#filterByRemovedFromTerrain').click(function () {
                if(this.value === "YES"){
                    this.value="NO";

                    $table.bootstrapTable('filterBy', {
                        // Reset filter
                    })
                }
                else if(this.value === "NO"){
                    this.value="YES";
                    $table.bootstrapTable('filterBy', {
                        removed: "True"
                    })
                }

            })
            $('#filterByNote').click(function () {
                if(this.value === "YES"){
                    this.value="NO";

                    $table.bootstrapTable('filterBy', {}, {
                        'filterAlgorithm': (row, filters) => {
                            return true;
                        }
                    })
                }
                else if(this.value === "NO"){
                    this.value="YES";
                    $table.bootstrapTable('filterBy', {}, {
                        'filterAlgorithm': (row, filters) => {
                            return row.note.length > 0
                        }
                    })
                }
            })
        })
    </script>
@endsection
