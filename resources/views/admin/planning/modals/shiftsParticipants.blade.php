<div class="modal fade" id="ShiftsParticipantsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="shiftForm" method="POST" action="/admin/planning/shift/participants">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Diensten koppelen</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="shiftsContainer">
                        @foreach($shifts as $index => $object)
                            <div class="input-group mb-3" id="shiftParticipants-{{$index}}">
                                <input type="hidden" name="shiftParticipants[{{ $index }}][id]" value="{{ $object->id }}">
                                <span class="input-group-text">Naam</span>
                                <input type="text" disabled  class="form-control flex-grow-1" value="{{$object->name}}" placeholder="Name" aria-label="Name" >
                                <span class="input-group-text">Categorie</span>
                                <input type="text"  disabled  class="form-control flex-grow-1" value="{{$object->shiftCategory->name}}" placeholder="Categorie" aria-label="Categorie" >
                                <span class="input-group-text">Max</span>
                                <input type="text" disabled style="max-width: 80px" class="form-control flex-grow-1" value="{{$object->participants->count() . " / " . $object->max_participants}}" placeholder="Max" aria-label="Max" >
                                <span class="input-group-text">Participants</span>
                                <select multiple class="form-control jemoeder" data-placeholder="Choose anything" id="shiftParticipants[{{ $index }}][shiftParticipants][]" name="shiftParticipants[{{ $index }}][shiftParticipants][]">
                                    @foreach($parents as $parent)
                                        @if($object->participants->contains($parent))
                                            <option value="{{ $parent->id }}" selected>{{ $parent->displayName() }}</option>
                                        @else
                                            <option value="{{ $parent->id }}">{{ $parent->displayName() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteObject({{$index}}, '{{ $object->id }}')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endforeach
                            <script>
                                $('.jemoeder').select2( {
                                    theme: "bootstrap-5",
                                    placeholder: $( this ).data( 'placeholder' ),
                                    closeOnSelect: false,
                                    dropdownParent: $('#ShiftsParticipantsModal')
                                } );
                            </script>
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



    let participantCount = {{ count($shifts) }};
    let deletedParticipants = [];
    // function addShift() {
    //     const container = document.getElementById('shiftsContainer');
    //     const newObject = document.createElement('div');
    //     newObject.className = 'shifts';
    //     newObject.id = `shifts-${shiftCount}`;
    //     newObject.innerHTML = `
    //                     <div class="input-group mb-3" id="shifts-${shiftCount}">
    //                         <input type="hidden" name="shifts[${shiftCount}][id]" value="${shiftCount}">
    //                         <span class="input-group-text">Naam</span>
    //                         <input type="text" class="form-control flex-grow-1" style="max-width: 100px" placeholder="Name" aria-label="Name" id="shifts[${shiftCount}][name]" name="shifts[${shiftCount}][name]" >
    //                         <span class="input-group-text">Categorie</span>
    //                         <select style="max-width: 100px" class="form-control" id="shifts[${shiftCount}][shiftCategory]" name="shifts[${shiftCount}][shiftCategory]">
    //                             ` + shiftCategories.map(shiftCategory => `<option value="${shiftCategory.id}">${shiftCategory.name}</option>`).join('') + `
    //                         </select>
    //                         <span class="input-group-text">Start datum</span>
    //                         <input type="datetime-local"  class="flex-grow-1 form-control" placeholder="Start datum" aria-label="Start date" id="shifts[${shiftCount}][start_time]" name="shifts[${shiftCount}][start_time]" >
    //                         <span class="input-group-text">Eind datum</span>
    //                         <input type="datetime-local"  class="flex-grow-1 form-control" placeholder="Eind datum" aria-label="Start date" id="shifts[${shiftCount}][end_time]" name="shifts[${shiftCount}][end_time]">
    //                         <span class="input-group-text">Max ouders</span>
    //                         <input type="number" style="max-width: 75px" min=0 class="flex-grow-1 input-group-text form-control" aria-label="max" id="shifts[${shiftCount}][max_participants]" name="shifts[${shiftCount}][max_participants]">
    //                         <button type="button" class="btn btn-outline-secondary" onclick="deleteObject(${shiftCount}, null)">
    //                             <span aria-hidden="true">&times;</span>
    //                         </button>
    //                     </div>
    //         `;
    //     container.appendChild(newObject);
    //     shiftCount++;
    // }
    //
    // function deleteObject(index, id) {
    //     const objectDiv = document.getElementById(`shifts-${index}`);
    //     if (id !== null) {
    //         deletedShifts.push(id);
    //     }
    //     objectDiv.remove();
    // }
    //
    // function appendDeletedObjects(form) {
    //     const input = document.createElement('input');
    //     input.type = 'hidden';
    //     input.name = 'deleted_shifts';
    //     input.value = JSON.stringify(deletedShifts);
    //     form.appendChild(input);
    // }
    //
    // document.getElementById('shiftForm').addEventListener('submit', function(event) {
    //     appendDeletedObjects(this);
    // });
</script>
