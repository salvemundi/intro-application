<div class="modal fade" id="ShiftsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="shiftForm" method="POST" action="/admin/planning/shift">
            @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Diensten</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-primary mb-2" onclick="addShift()"><i class="fas fa-plus"></i></button>
                    <div id="shiftsContainer">
                        @foreach($shifts as $index => $object)
                            <div class="input-group mb-3" id="shifts-{{$index}}">
                                <input type="hidden" name="shifts[{{ $index }}][id]" value="{{ $object->id }}">
                                <span class="input-group-text">Naam</span>
                                <input type="text" style="max-width: 100px" class="form-control flex-grow-1" value="{{$object->name}}" placeholder="Name" aria-label="Name" id="shifts[{{ $index }}][name]" name="shifts[{{$index}}][name]" >
                                <span class="input-group-text">Categorie</span>
                                <select style="max-width: 100px" class="form-control" id="shifts[{{ $index }}][shiftCategory]" name="shifts[{{ $index }}][shiftCategory]">
                                    @foreach($categories as $category)
                                        @if($category->id === $object->shiftCategory->id)
                                            <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                        @else
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <span class="input-group-text">Start datum</span>
                                <input type="datetime-local" value="{{$object->start_time}}" class="flex-grow-1 form-control" placeholder="Start datum" aria-label="Start date" id="shifts[{{$index}}][start_time]" name="shifts[{{$index}}][start_time]" >
                                <span class="input-group-text">Eind datum</span>
                                <input  type="datetime-local" value="{{$object->end_time}}"  class="flex-grow-1 form-control" placeholder="Eind datum" aria-label="Start date" id="shifts[{{$index}}][end_time]" name="shifts[{{$index}}][end_time]">
                                <span class="input-group-text">Max ouders</span>
                                <input type="number" min=0 value="{{$object->max_participants}}" style="max-width: 75px"  class="flex-grow-1 input-group-text form-control" aria-label="max" id="shifts[{{$index}}][max_participants]" name="shifts[{{$index}}][max_participants]">
                                <button type="button" class="btn btn-outline-danger" onclick="deleteObject({{$index}}, '{{ $object->id }}')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
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
    let shiftCount = {{ count($shifts) }};
    let shiftCategories = @json($categories);
    let deletedShifts = [];
    function addShift() {
        const container = document.getElementById('shiftsContainer');
        const newObject = document.createElement('div');
        newObject.className = 'shifts';
        newObject.id = `shifts-${shiftCount}`;
        newObject.innerHTML = `
                        <div class="input-group mb-3" id="shifts-${shiftCount}">
                            <input type="hidden" name="shifts[${shiftCount}][id]" value="${shiftCount}">
                            <span class="input-group-text">Naam</span>
                            <input type="text" class="form-control flex-grow-1" style="max-width: 100px" placeholder="Name" aria-label="Name" id="shifts[${shiftCount}][name]" name="shifts[${shiftCount}][name]" >
                            <span class="input-group-text">Categorie</span>
                            <select style="max-width: 100px" class="form-control" id="shifts[${shiftCount}][shiftCategory]" name="shifts[${shiftCount}][shiftCategory]">
                                ` + shiftCategories.map(shiftCategory => `<option value="${shiftCategory.id}">${shiftCategory.name}</option>`).join('') + `
                            </select>
                            <span class="input-group-text">Start datum</span>
                            <input type="datetime-local"  class="flex-grow-1 form-control" placeholder="Start datum" aria-label="Start date" id="shifts[${shiftCount}][start_time]" name="shifts[${shiftCount}][start_time]" >
                            <span class="input-group-text">Eind datum</span>
                            <input type="datetime-local"  class="flex-grow-1 form-control" placeholder="Eind datum" aria-label="Start date" id="shifts[${shiftCount}][end_time]" name="shifts[${shiftCount}][end_time]">
                            <span class="input-group-text">Max ouders</span>
                            <input type="number" style="max-width: 75px" min=0 class="flex-grow-1 input-group-text form-control" aria-label="max" id="shifts[${shiftCount}][max_participants]" name="shifts[${shiftCount}][max_participants]">
                            <button type="button" class="btn btn-outline-danger" onclick="deleteObject(${shiftCount}, null)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
            `;
        container.appendChild(newObject);
        shiftCount++;
    }

    function deleteObject(index, id) {
        const objectDiv = document.getElementById(`shifts-${index}`);
        if (id !== null) {
            deletedShifts.push(id);
        }
        objectDiv.remove();
    }

    function appendDeletedObjects(form) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_shifts';
        input.value = JSON.stringify(deletedShifts);
        form.appendChild(input);
    }

    document.getElementById('shiftForm').addEventListener('submit', function(event) {
        appendDeletedObjects(this);
    });
</script>
