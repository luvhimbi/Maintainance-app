@extends('Layouts.AdminNavBar')

@section('title', 'Add New Building')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Add New Building</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.buildings.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title mb-4">Building Information</h6>
                                        
                                        <div class="mb-3">
                                            <label for="building_name" class="form-label">Building Name</label>
                                            <input type="text" 
                                                   class="form-control @error('building_name') is-invalid @enderror" 
                                                   id="building_name" 
                                                   name="building_name" 
                                                   value="{{ old('building_name') }}" 
                                                   required>
                                            @error('building_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <input type="text" 
                                                   class="form-control @error('latitude') is-invalid @enderror" 
                                                   id="latitude" 
                                                   name="latitude" 
                                                   value="{{ old('latitude') }}" 
                                                   required>
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="text" 
                                                   class="form-control @error('longitude') is-invalid @enderror" 
                                                   id="longitude" 
                                                   name="longitude" 
                                                   value="{{ old('longitude') }}" 
                                                   required>
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title mb-4">Location Map</h6>
                                        <div id="map" class="rounded" style="height: 300px;"></div>
                                        <div class="alert alert-info mt-3 mb-0">
                                            Click on the map to set the building location
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.buildings.index') }}" class="btn btn-light">
                                Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Save Building
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css' rel='stylesheet' />

<script>
mapboxgl.accessToken = '{{ env('MAPBOX_ACCESS_TOKEN') }}';
const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/satellite-v9',
    center: [28.098117949467696, -25.540749664800238],
    zoom: 16
});

let marker = null;

map.on('load', () => {
    map.addControl(new mapboxgl.NavigationControl());
    map.addControl(new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true
        },
        trackUserLocation: true
    }));
});

map.on('click', (e) => {
    const { lng, lat } = e.lngLat;
    
    document.getElementById('longitude').value = lng;
    document.getElementById('latitude').value = lat;
    
    if (marker) {
        marker.remove();
    }
    
    marker = new mapboxgl.Marker()
        .setLngLat([lng, lat])
        .addTo(map);
});

// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>
@endpush
@endsection 