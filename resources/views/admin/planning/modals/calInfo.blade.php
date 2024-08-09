<div class="modal fade" id="calInfo-{{$event->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{$event->name}} -  {{\Carbon\Carbon::parse($event->start_time)->format('H:i') . " - " . \Carbon\Carbon::parse($event->end_time)->format('H:i')}}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Dienst leider: {{$event->shiftCategory->shiftLeader->displayName()}}
                <br><br>
                Deelnemers:<br>
                @foreach($event->participants as $participant)
                    {{$participant->displayName()}}<br>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
