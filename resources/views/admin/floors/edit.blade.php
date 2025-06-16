@extends('Layouts.AdminNavBar')

@section('title', 'Edit Floor')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Edit Floor</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.floors.update', $floor->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title mb-4">Floor Information</h6>

                                        <div class="mb-3">
                                            <label for="building_id" class="form-label">Building</label>
                                            <select class="form-select @error('building_id') is-invalid @enderror"
                                                    id="building_id"
                                                    name="building_id"
                                                    required>
                                                <option value="">Select a building</option>
                                                @foreach($buildings as $building)
                                                    <option value="{{ $building->id }}"
                                                            {{ old('building_id', $floor->building_id) == $building->id ? 'selected' : '' }}>
                                                        {{ $building->building_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('building_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="floor_number" class="form-label">Floor Number</label>
                                            <input type="text" 
                                                   class="form-control @error('floor_number') is-invalid @enderror" 
                                                   id="floor_number" 
                                                   name="floor_number" 
                                                   value="{{ old('floor_number', $floor->floor_number) }}" 
                                                   required>
                                            @error('floor_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title mb-4">Room Information (Optional)</h6>

                                        <div class="mb-3">
                                            <label class="form-label">Rooms</label>
                                            <div id="rooms-container">
                                                @foreach($floor->rooms as $room)
                                                    <div class="room-entry mb-3">
                                                        <div class="d-flex gap-2">
                                                            <div class="flex-grow-1">
                                                                <input type="text" 
                                                                       class="form-control" 
                                                                       name="room_numbers[]" 
                                                                       value="{{ $room->room_number }}"
                                                                       placeholder="Room number">
                                                            </div>
                                                            <button type="button" class="btn btn-outline-danger remove-room">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($floor->rooms->isEmpty())
                                                    <div class="room-entry mb-3">
                                                        <div class="d-flex gap-2">
                                                            <div class="flex-grow-1">
                                                                <input type="text" 
                                                                       class="form-control" 
                                                                       name="room_numbers[]" 
                                                                       placeholder="Room number">
                                                            </div>
                                                            <button type="button" class="btn btn-outline-danger remove-room" style="display: none;">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-outline-primary mt-2" id="add-room">
                                                <i class="fas fa-plus me-2"></i>Add Room
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.floors.index') }}" class="btn btn-light">
                                Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update Floor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomsContainer = document.getElementById('rooms-container');
    const addRoomButton = document.getElementById('add-room');
    const initialRoomEntry = roomsContainer.querySelector('.room-entry');

    // Show remove button if there's more than one room
    function updateRemoveButtons() {
        const roomEntries = roomsContainer.querySelectorAll('.room-entry');
        roomEntries.forEach(entry => {
            const removeButton = entry.querySelector('.remove-room');
            removeButton.style.display = roomEntries.length > 1 ? 'block' : 'none';
        });
    }

    // Add new room entry
    addRoomButton.addEventListener('click', function() {
        const newRoomEntry = initialRoomEntry.cloneNode(true);
        newRoomEntry.querySelector('input').value = '';
        roomsContainer.appendChild(newRoomEntry);
        updateRemoveButtons();
    });

    // Remove room entry
    roomsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-room')) {
            e.target.closest('.room-entry').remove();
            updateRemoveButtons();
        }
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endpush
@endsection
