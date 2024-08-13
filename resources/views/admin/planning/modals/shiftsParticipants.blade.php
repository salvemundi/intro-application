<div class="modal fade" id="ShiftsParticipantsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="shiftForm" method="POST" action="/admin/planning/shift/participants">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Diensten koppelen</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="shiftsContainer">
                        <hr class="my-3">

                        @foreach($shifts as $index => $object)
                            <div class="input-group mb-3 d-block" id="shiftParticipants-{{$index}}">
                                <input type="hidden" name="shiftParticipants[{{ $index }}][id]" value="{{ $object->id }}">

                                <div class="row g-2">
                                    <div class="col-12 col-md-3 mb-2 mb-md-0">
                                        <span class="input-group-text w-100">Naam</span>
                                        <input type="text" disabled class="form-control" value="{{$object->name}}" placeholder="Name" aria-label="Name">
                                    </div>
                                    <div class="col-12 col-md-3 mb-2 mb-md-0">
                                        <span class="input-group-text w-100">Categorie</span>
                                        <input type="text" disabled class="form-control" value="{{$object->shiftCategory->name}}" placeholder="Categorie" aria-label="Categorie">
                                    </div>
                                    <div class="col-12 col-md-2 mb-2 mb-md-0">
                                        <span class="input-group-text w-100">Max</span>
                                        <input type="text" disabled class="form-control" style="max-width: 80px;" value="{{$object->participants->count() . " / " . $object->max_participants}}" placeholder="Max" aria-label="Max">
                                    </div>
                                    <div class="col-12 col-md-3 mb-2 mb-md-0">
                                        <span class="input-group-text w-100">Participants</span>
                                        <select multiple class="form-control jemoeder" data-placeholder="Choose anything" id="shiftParticipants[{{ $index }}][shiftParticipants][]" name="shiftParticipants[{{ $index }}][shiftParticipants][]">
                                            @foreach($parentsAndCrew as $parent)
                                                @if($object->participants->contains($parent))
                                                    <option value="{{ $parent->id }}" selected>{{ $parent->displayName() }}</option>
                                                @else
                                                    <option value="{{ $parent->id }}">{{ $parent->displayName() }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">

                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.jemoeder').select2({
        theme: "bootstrap-5",
        placeholder: $(this).data('placeholder'),
        closeOnSelect: false,
        dropdownParent: $('#ShiftsParticipantsModal')
    });

    function deleteObject(index, id) {
        const objectDiv = document.getElementById(`shiftParticipants-${index}`);
        if (id !== null) {
            deletedObjects.push(id);
        }
        objectDiv.remove();
    }

    function appendDeletedObjects(form) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_objects';
        input.value = JSON.stringify(deletedObjects);
        form.appendChild(input);
    }

    document.querySelector('form').addEventListener('submit', function(event) {
        appendDeletedObjects(this);
    });
</script>
