<div class="modal fade" id="ShiftsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="shiftForm" method="POST" action="/admin/planning/shift">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Diensten</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <button type="button" class="btn btn-primary mb-2" onclick="addShift()"><i class="fas fa-plus"></i></button>
                    <div id="shiftsContainer">
                        <hr class="my-3">
                        @foreach($shifts as $index => $object)
                            <div class="input-group mb-3" id="shifts-{{$index}}">
                                <input type="hidden" name="shifts[{{ $index }}][id]" value="{{ $object->id }}">
                                <div class="row g-2">
                                    <div class="col-12 col-md-2">
                                        <span class="input-group-text w-100">Naam</span>
                                        <input type="text" class="form-control" value="{{$object->name}}" placeholder="Name" aria-label="Name" id="shifts[{{ $index }}][name]" name="shifts[{{$index}}][name]" >
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <span class="input-group-text w-100">Categorie</span>
                                        <select class="form-control" id="shifts[{{ $index }}][shiftCategory]" name="shifts[{{ $index }}][shiftCategory]">
                                            @foreach($categories as $category)
                                                @if($category->id === $object->shiftCategory->id)
                                                    <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                                                @else
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <span class="input-group-text w-100">Start datum</span>
                                        <input type="datetime-local" value="{{$object->start_time}}" class="form-control" placeholder="Start datum" aria-label="Start date" id="shifts[{{$index}}][start_time]" name="shifts[{{$index}}][start_time]" >
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <span class="input-group-text w-100">Eind datum</span>
                                        <input type="datetime-local" value="{{$object->end_time}}" class="form-control" placeholder="Eind datum" aria-label="Start date" id="shifts[{{$index}}][end_time]" name="shifts[{{$index}}][end_time]">
                                    </div>
                                    <div class="col-8 col-md-2">
                                        <span class="input-group-text w-100">Max ouders</span>
                                        <input type="number" min=0 value="{{$object->max_participants}}" class="form-control" aria-label="max" id="shifts[{{$index}}][max_participants]" name="shifts[{{$index}}][max_participants]">
                                    </div>
                                    <div class="col-4 col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="deleteShift({{$index}}, '{{ $object->id }}')">
                                            <span aria-hidden="true"><i class="fas fa-trash"></i></span>
                                        </button>
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
    let shiftCount = {{ count($shifts) }};
    let shiftCategories = @json($categories);
    let deletedShifts = [];
    function addShift() {
        const container = document.getElementById('shiftsContainer');
        const newObject = document.createElement('div');
        newObject.className = 'shifts';
        newObject.id = `shifts-${shiftCount}`;
        newObject.innerHTML = `
                            <div class="input-group mb-3 d-block" id="shifts-${shiftCount}">
                                <div class="row g-2">
                                    <div class="col-12 col-md-2">
                                        <span class="input-group-text w-100">Naam</span>
                                        <input type="text" class="form-control"  placeholder="Name" aria-label="Name" id="shifts[${shiftCount}][name]" name="shifts[${shiftCount}][name]" >
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <span class="input-group-text w-100">Categorie</span>
                                        <select class="form-control" id="shifts[${shiftCount}][shiftCategory]" name="shifts[${shiftCount}][shiftCategory]">
                                            ` + shiftCategories.map(shiftCategory => `<option value="${shiftCategory.id}">${shiftCategory.name}</option>`).join('') + `
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                    <span class="input-group-text w-100">Start datum</span>
                                    <input type="datetime-local"  class="form-control" placeholder="Start datum" aria-label="Start date" id="shifts[${shiftCount}][start_time]" name="shifts[${shiftCount}][start_time]" >
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <span class="input-group-text w-100">Eind datum</span>
                                        <input type="datetime-local"  class="form-control" placeholder="Eind datum" aria-label="End date" id="shifts[${shiftCount}][end_time]" name="shifts[${shiftCount}][end_time]">
                                    </div>
                                    <div class="col-8 col-md-2">
                                        <span class="input-group-text w-100">Max ouders</span>
                                        <input type="number" min=0 class="form-control" aria-label="max" id="shifts[${shiftCount}][max_participants]" name="shifts[${shiftCount}][max_participants]">
                                    </div>
                                    <div class="col-4 col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="deleteShift(${shiftCount}}, null)">
                                            <span aria-hidden="true"><i class="fas fa-trash"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
            `;
        container.appendChild(newObject);
        shiftCount++;
    }

    function deleteShift(index, id) {
        const objectDiv = document.getElementById(`shifts-${index}`);
        if (id !== null) {
            deletedShifts.push(id);
        }
        objectDiv.remove();
    }

    function appendDeletedShifts(form) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_shifts';
        input.value = JSON.stringify(deletedShifts);
        form.appendChild(input);
    }

    document.getElementById('shiftForm').addEventListener('submit', function(event) {
        appendDeletedShifts(this);
    });
</script>
