@extends('layouts.app')
@section('content')
<script>
setActive("participants");
</script>
<div class="row">
    @if(!Request::is('participants'))
        <div class="col-12 col-md-6 container">
    @else
        <div class="col-12 container">
    @endif
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
    @if(session()->has('message'))
        <div class="alert alert-primary">
            {{ session()->get('message') }}
        </div>
    @endif
        <div class="d-flex">

            <div class="dropdown" style="">
                <button class="btn btn-secondary dropdown-toggle" style="width: auto !important;" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <li><a class="dropdown-item" href="{{ route('export_excel.excel')}}">Export checked in to Excel</a></li>
                    <li><a class="dropdown-item" href="{{ route('fontysEmail.excel')}}">Export student fontys mails</a></li>
                    <li><a class="dropdown-item" href="{{ route('export_excel.all')}}">Export alle betaalde/Purple</a></li>
                    <li><a class="dropdown-item" href="{{ route('exportParticipants.excel')}}">Export deelnemers lid</a></li>
                </ul>
            </div>

            <div class="dropdown" style="margin-left: 4px;">
                <button class="btn btn-secondary dropdown-toggle" style="width: auto !important;" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <li><button class="dropdown-item" id="filterByNone" value="NO" type="button">#NoFilter</button></li>
                    <li><button class="dropdown-item" id="filterByCheckedInOnly" value="NO" type="button">Ingechecked</button></li>
                    <li><button class="dropdown-item" id="filterByRemovedFromTerrain" value="NO" type="button">Verbannen deelnemers</button></li>
                    <li><button class="dropdown-item" id="filterByNote" value="NO" type="button">Deelnemers met opmerking</button></li>
                    <li><button class="dropdown-item" id="filterByPurpleOnly" value="NO" type="button">Alleen purple deelnemers</button></li>
                    <li><button class="dropdown-item" id="filterByStudytypeDemandBased" value="NO" type="button">Alleen Demand Based</button></li>
                    <li><button class="dropdown-item" id="filterByStudytypeCourseBased" value="NO" type="button">Alleen Course Based</button></li>
                    <li><button class="dropdown-item" id="filterByStudytypeUnknown" value="NO" type="button">Alleen Onbekende Studytype</button></li>
                </ul>
            </div>

            <div class="dropdown" style="margin-left: 4px;">
                <button class="btn btn-secondary dropdown-toggle" style="width: auto !important;" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Mailing
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <li>
                        <form method="POST" action="/registrations">
                            @csrf
                            <button type="submit" class="dropdown-item">Stuur betaling email</button>
                        </form>
                    </li>

                    <li>
                        <form method="POST" action="/participants/resendQRcode">
                            @csrf
                            <button type="submit" class="dropdown-item">Stuur QR-code kiddos</button>
                        </form>
                    </li>
                    <li>

                        <form method="POST" action="/participants/resendQRcodeNonParticipants">
                            @csrf
                            <button type="submit" class="dropdown-item">Stuur QR-code non kiddos</button>
                        </form>

                    </li>
                </ul>
            </div>

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

        <h4 class="mt-3">Paarse achtegrond = Alleen naar purple inschrijving</h4>

        <div class="table-responsive">
            <table id="table" data-toggle="table" data-search="true" data-sortable="true" data-pagination="true"
            data-show-columns="true">

                <thead>
                    <tr class="tr-class-1">
                        <th data-field="firstName" data-sortable="true">Naam</th>
                        <th data-field="role" data-sortable="true">Rol</th>
                        <th data-field="checkedIn" data-sortable="true">Checked in</th>
                        <th data-field="data" data-sortable="true">Gegevens</th>
                        @if(Request::is('participants'))
                            <th data-field="createdat" data-sortable="true">Laatste aanpassing</th>
                            <th data-field="daysDif" data-sortable="true">Dagen geleden ingeschreven</th>
                        @endif
                        <th data-field="paid" data-sortable="true">Betaald</th>
                        <th data-field="note" data-sortable="false">Notitie</th>
                        <th data-field="purpleOnly" data-sortable="false">Alleen Purple?</th>
                        <th data-field="removed" data-sortable="false">Verwijderd</th>
                        <th data-field="email" data-sortable="false">email</th>
                        <th data-field="fontysEmail" data-sortable="false">Fontys email</th>
                        <th data-field="studyType" data-sortable="false">Study Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participants as $participant)
                        <tr id="tr-id-3" class="tr-class-2" data-title="bootstrap table">
                            @if($participant->purpleOnly == 1)
                                @if($participant->firstName == null || $participant->firstName == "")
                                    <td class="purpleOnly" data-value="Ontbreekt">Ontbreekt</td>
                                @else
                                    <td class="purpleOnly" data-value="{{ $participant->firstName }}">{{ $participant->firstName }} {{ $participant->lastName }}</td>
                                @endif
                            @else
                                <td data-value="{{ $participant->firstName }}">{{ $participant->firstName }} {{ $participant->lastName }}</td>
                            @endif
                            <td data-value="{{ $participant->role }}">{{ \App\Enums\Roles::fromValue($participant->role)->description }}</td>

                            @if($participant->checkedIn == 1)
                                <td data-value="{{ $participant->checkedIn }}">
                                    <span class="badge rounded-pill bg-success">Ingecheckt</span>
                                </td>
                            @else
                                <td data-value="{{ $participant->checkedIn }}">
                                    <span class="badge rounded-pill bg-danger">Uitgecheckt</span>
                                </td>
                            @endif
                            <td data-value="{{ $participant->id }}"><a href="/participants/{{$participant->id}}"><button type="button" class="btn btn-primary">Details</button></a></td>
                            @if(Request::is('participants'))
                                <td data-value="{{ $participant->firstName }}">{{ $participant->updated_at }}</td>
                                <td data-value="{{ $participant->dateDifference }}">{{ $participant->dateDifference }}</td>
                            @endif
                            <td data-value="{{ $participant->paid }}">
                                @if($participant->latestPayment)
                                    @if($participant->latestPayment->paymentStatus == \App\Enums\PaymentStatus::paid)
                                        <span class="badge rounded-pill bg-success text-black">Betaald</span>
                                    @elseif($participant->latestPayment->paymentStatus == \App\Enums\PaymentStatus::pending)
                                        <span class="badge rounded-pill bg-warning text-black">In behandeling</span>
                                    @elseif($participant->latestPayment->paymentStatus == \App\Enums\PaymentStatus::canceled)
                                        <span class="badge rounded-pill bg-secondary">Geannuleerd</span>
                                    @elseif($participant->latestPayment->paymentStatus == \App\Enums\PaymentStatus::expired)
                                        <span class="badge rounded-pill bg-secondary">Verlopen</span>
                                    @elseif($participant->latestPayment->paymentStatus == \App\Enums\PaymentStatus::failed)
                                        <span class="badge rounded-pill bg-danger">Gefaald</span>
                                    @elseif($participant->latestPayment->paymentStatus == \App\Enums\PaymentStatus::open)
                                        <span class="badge rounded-pill bg-secondary">Open</span>
                                    @endif
                                @else
                                    <span class="badge rounded-pill bg-secondary">Geen transacties</span>
                                @endif
                            </td>
                            <td data-value="{{ $participant->note }}">{{ $participant->note }}</td>
                            @if($participant->purpleOnly == 1)
                                <td data-value="{{ $participant->purpleOnly }}">Ja</td>
                            @else
                                <td data-value="{{ $participant->purpleOnly }}">Nee</td>
                            @endif
                            @if($participant->removedFromIntro == 1)
                                <td data-value="{{ $participant->removedFromIntro }}">Ja</td>
                            @else
                                <td data-value="{{ $participant->removedFromIntro }}">Nee</td>
                            @endif
                            <td data-value="{{ $participant->email }}">{{$participant->email}}</td>
                            <td data-value="{{ $participant->fontysEmail }}">{{$participant->fontysEmail}}</td>
                            <td data-value="{{ $participant->studyType }}">{{$participant->studyType}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="center mt-2">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#checkoutEveryoneModal">
                    Check allen uit
                </button>
            </div>
        </div>
    </div>
    @if(!Request::is('participants'))
    <div class="col-12 col-md-6 container mb-5">
        @isset($selectedParticipant)
            @include('include.participantEditModal', ['participant' => $selectedParticipant])
            @include('include.participantConfirmationMailModal', ['participant' => $selectedParticipant])
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $selectedParticipant->firstName }} {{ $selectedParticipant->insertion ?? null}} {{ $selectedParticipant->lastName }}</h5>
                    <p class="card-text">Gegevens:</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">ID: {{$selectedParticipant->id}}</li>
                    <li class="list-group-item">Leeftijd: {{ \Carbon\Carbon::parse($selectedParticipant->birthday)->diff(\Carbon\Carbon::now())->format('%y years') }}</li>
                    <li class="list-group-item">E-mail: {{$selectedParticipant->email}}</li>
                    <li class="list-group-item">Telefoon nummer: {{ $selectedParticipant->phoneNumber }}</li>
                    <li class="list-group-item">Allergieën: {{ $selectedParticipant->medicalIssues ?? "N.v.t" }}</li>
                    <li class="list-group-item">Bijzonderheden: {{ $selectedParticipant->specials ?? "N.v.t" }}</li>

                    @if($selectedParticipant->role == \App\Enums\Roles::child)
                        <li class="list-group-item">Leerjaar: {{ App\Enums\StudentYear::fromvalue($selectedParticipant->studentYear)->key}}</li>
                        <li class="list-group-item">Naam Ouder: {{ $selectedParticipant->firstNameParent}} {{ $selectedParticipant->lastNameParent}}</li>
                        <li class="list-group-item">Adres Ouder: {{ $selectedParticipant->addressParent}}</li>
                        <li class="list-group-item">Telefoonnummer ouder: {{ $selectedParticipant->phoneNumberParent}}</li>
                        <li class="list-group-item">Studie type: {{ App\Enums\StudyType::coerce($selectedParticipant->studyType)->description}}</li>
                    @endif
                    <li class="list-group-item">Opmerking: {{ $selectedParticipant->note}}</li>
                </ul>
                <div class="card-body">
                    <div class="d-flex flex-sm-row flex-column ">
                        @if (!$selectedParticipant->checkedIn)
                            <form method="post" class="center" action="/participants/{{ $selectedParticipant->id }}/checkIn">
                                @csrf
                                <button type="submit" href="#" style="visibility: visible !important;" class="card-link card-link-button">Check in</button>
                            </form>
                        @else
                            <form method="post" class="center" action="/participants/{{ $selectedParticipant->id }}/checkOut">
                                @csrf
                                <button type="submit" href="#" style="visibility: visible !important;" class="card-link card-link-button">Check uit</button>
                            </form>
                        @endif

                        <button href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="card-link card-link-button">Verwijder</button>
                        <button class="card-link card-link-button" data-bs-toggle="modal" data-bs-target="#confirmationMailModal{{$selectedParticipant->id}}">
                            Stuur confirmatie mail
                        </button>
                        <button type="button" class="card-link card-link-button" data-bs-toggle="modal" data-bs-target="#edit{{ $selectedParticipant->id }}">
                            Bewerk
                        </button>
                        <form method="post" class="center" action="/participants/{{ $selectedParticipant->id }}/storeRemove">
                            @csrf
                            @if(!$selectedParticipant->removedFromIntro)
                                <button type="submit" class="card-link card-link-button">Verban deelnemer van terrein / intro</button>
                            @else
                                <button type="submit" class="card-link card-link-button">Laat deelnemer weer toe op terrein / intro</button>
                            @endif
                        </form>
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
    @endif
    <script>
        var $table = $('#table')


        $(function() {
            $table.bootstrapTable('hideColumn',['note','removed','purpleOnly','email','fontysEmail','studyType'])
            resetFilter();
            $('#filterByCheckedInOnly').click(function () {
                resetFilter()
                $table.bootstrapTable('filterBy', {
                    checkedIn: "True",
                    purpleOnly: "Nee"
                })
            })
            $('#filterByRemovedFromTerrain').click(function () {
                resetFilter()
                $table.bootstrapTable('filterBy', {
                    removed: "Ja",
                    purpleOnly: "Nee"
                })
            })
            $('#filterByNote').click(function () {
                resetFilter()

                $table.bootstrapTable('filterBy', {}, {
                    'filterAlgorithm': (row, filters) => {
                        return row.note.length > 0 && row.purpleOnly == "Nee"
                    }
                })
            })

            $('#filterByPurpleOnly').click(function () {
                resetFilter()

                $table.bootstrapTable('filterBy', {
                    purpleOnly: "Ja"
                })
            })

            $('#filterByStudytypeDemandBased').click(function () {
                resetFilter()

                $table.bootstrapTable('filterBy', {
                    studyType: "0"
                })
            })

            $('#filterByStudytypeCourseBased').click(function () {
                resetFilter()

                $table.bootstrapTable('filterBy', {
                    studyType: "1"
                })
            })

            $('#filterByStudytypeUnknown').click(function () {
                resetFilter()

                $table.bootstrapTable('filterBy', {
                    studyType: "2"
                })
            })

            $('#filterByNone').click(function () {
                resetFilter()
            })
        })

        function resetFilter() {
            $table.bootstrapTable('filterBy', {}, {
                'filterAlgorithm': 'and'
            })
            $table.bootstrapTable('filterBy', {
                purpleOnly: "Nee"
            })
        }

    </script>
@endsection
