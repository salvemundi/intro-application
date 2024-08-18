<div class="modal fade" id="OverlappingShiftsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Overlappende Diensten</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="shiftsContainer">
                        <hr class="my-3">

                        @foreach($overlappingShifts as $index => $object)
                            <div class="input-group mb-3 d-block" id="shiftParticipants-{{$index}}">
                                <div class="row g-2">
                                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                                        <span class="input-group-text w-100">Naam</span>
                                        <input type="text" disabled class="form-control" value="{{$object['participant']}}" placeholder="Name" aria-label="Name">
                                    </div>
                                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                                        <span class="input-group-text w-100">Overlapping</span>
                                        @foreach($object['overlappedShifts'] as $shift)
                                            <input type="text" disabled class="form-control" value="{{$shift}}" placeholder="Categorie" aria-label="Categorie">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>
