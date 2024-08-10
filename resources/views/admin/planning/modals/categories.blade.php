<div class="modal fade" id="CategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="/admin/planning/shift/category">
                <div class="modal-header">
                    <h5 class="modal-title">Categorieën</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <button type="button" class="btn btn-primary mb-2" onclick="addObject()"><i class="fas fa-plus"></i></button>
                    @csrf
                    <div id="objectsContainer">
                        <hr class="my-3">
                        @foreach($categories as $index => $object)
                            <div class="input-group mb-3 d-block" id="object-{{$index}}">
                                <input type="hidden" name="objects[{{ $index }}][id]" value="{{ $object->id }}">

                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <span class="input-group-text w-100">Dienst categorie</span>
                                        <input type="text" class="form-control" value="{{$object->name}}" placeholder="Name" aria-label="Name" id="objects[{{ $index }}][name]" name="objects[{{$index}}][name]" >
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <span class="input-group-text w-100">Dienstleider</span>
                                        <select class="form-control" id="objects[{{ $index }}][shiftLeader]" name="objects[{{ $index }}][shiftLeader]">
                                            @foreach($shiftLeaders as $shiftLeader)
                                                @if($shiftLeader->id === $object->shiftLeader->id)
                                                    <option value="{{ $shiftLeader->id }}" selected>{{ $shiftLeader->firstName }}</option>
                                                @else
                                                    <option value="{{ $shiftLeader->id }}">{{ $shiftLeader->firstName }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8 col-md-3">
                                        <span class="input-group-text w-100">Kleur</span>
                                        <input type="color" value="{{$object->color}}" class="form-control form-control-color" placeholder="Kleur" aria-label="Color" id="objects[{{$index}}][color]" name="objects[{{$index}}][color]" >
                                    </div>
                                    <div class="col-4 col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="deleteObject({{$index}}, '{{ $object->id }}')">
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
    let objectCount = {{ count($categories) }};
    let shiftLeaders = @json($shiftLeaders);
    let deletedObjects = [];

    function addObject() {
        const container = document.getElementById('objectsContainer');
        const newObject = document.createElement('div');
        newObject.className = 'object';
        newObject.id = `object-${objectCount}`;
        newObject.innerHTML = `
            <div class="input-group mb-3">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <span class="input-group-text w-100">Dienst categorie</span>
                        <input type="text" class="form-control" placeholder="Name" aria-label="Name" id="objects[${objectCount}][name]" name="objects[${objectCount}][name]" >
                    </div>
                    <div class="col-12 col-md-4">
                        <span class="input-group-text w-100">Dienstleider</span>
                        <select class="form-control" id="objects[${objectCount}][shiftLeader]" name="objects[${objectCount}][shiftLeader]">
                            ` + shiftLeaders.map(shiftLeader => `<option value="${shiftLeader.id}">${shiftLeader.firstName}</option>`).join('') + `
                        </select>
                    </div>
                    <div class="col-8 col-md-3">
                        <span class="input-group-text w-100">Kleur</span>
                        <input type="color" class="form-control form-control-color" placeholder="Kleur" aria-label="Color" id="objects[${objectCount}][color]" name="objects[${objectCount}][color]" >
                    </div>
                    <div class="col-4 col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="deleteObject(${objectCount}, null)">
                            <span aria-hidden="true"><i class="fas fa-trash"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newObject);
        objectCount++;
    }

    function deleteObject(index, id) {
        const objectDiv = document.getElementById(`object-${index}`);
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
