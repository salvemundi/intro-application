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

            <div class="dropdown" style="margin-left: 4px;">
                <button class="btn btn-secondary dropdown-toggle" style="width: auto !important;" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <li><a class="dropdown-item" href="{{ route('export_excel.excel')}}">Export checked in to Excel</a></li>
                    <li><a class="dropdown-item" href="{{ route('fontysEmail.excel')}}">Export student fontys mails</a></li>
                    <li><a class="dropdown-item" href="{{ route('export_excel.all')}}">Export all paid/Purple</a></li>
                    <li><a class="dropdown-item" href="{{ route('exportParticipants.excel')}}">Export participants membership</a></li>
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
            <button type="button" class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#checkoutEveryoneModal">
                Check allen uit
            </button>

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

        <h4 class="mt-3">Paarse achtegrond = Aleen naar purple inschrijving</h4>

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
                                    <span class="badge rounded-pill bg-success text-black">Ingecheckt</span>
                                </td>
                            @else
                                <td data-value="{{ $participant->checkedIn }}">
                                    <span class="badge rounded-pill bg-danger text-black">Uitgecheckt</span>
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
        </div>
    </div>
    @if(!Request::is('participants'))
    <div class="col-12 col-md-6 container mb-5">
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
                            <b>Id:</b> {{ $selectedParticipant->id}}<br>
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
                                <b>Studie type: </b> {{ App\Enums\StudyType::coerce($selectedParticipant->studyType)->description}}<br>
                            @endif
                            @if($selectedParticipant->role == \App\Enums\Roles::dad_mom)
                                <b>Naam Ouder:</b> {{ $selectedParticipant->firstNameParent}} {{ $selectedParticipant->lastNameParent}}<br>
                                <b>Telefoonnummer ouder:</b> {{ $selectedParticipant->phoneNumberParent}}<br>
                            @endif
                            <b>Allergieën:</b> {{ $selectedParticipant->medicalIssues}}<br>
                            <b>Bijzonderheden:</b> {{ $selectedParticipant->specials}}<br>

                            <div style="display: flex; flex-direction: row;">
                                @include('include.participantEditModal', ['participant' => $selectedParticipant])
                                @include('include.participantConfirmationMailModal', ['participant' => $selectedParticipant])
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#edit{{ $selectedParticipant->id }}">
                                    Bewerk
                                </button>
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
                                <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Verwijderen
                                </button>
                                <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmationMailModal{{$selectedParticipant->id}}">
                                    Confirmatie mail
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
                                    <button type="submit" class="btn btn-danger">Verban deelnemer van terrein / intro</button>
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
