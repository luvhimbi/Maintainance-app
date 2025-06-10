@extends('layouts.AdminNavBar')

@section('title', 'Add Location')

@section('content')
<div class="container-fluid location-edit py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div>
                <h2 class="h5 mb-1">Add New Location</h2>
                <p class="text-muted small mb-0">Enter the details for the new location</p>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body px-4">
            <form method="POST" action="{{ route('admin.locations.store') }}" id="createLocationForm">
                @csrf
                <div class="row mb-4">
                    <!-- Building Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Building Name <span class="text-danger">*</span></label>
                        <input type="text" name="building_name" class="form-control @error('building_name') is-invalid @enderror" 
                               value="{{ old('building_name') }}" required>
                        @error('building_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Floor Number -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Floor Number <span class="text-danger">*</span></label>
                        <input type="text" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror" 
                               value="{{ old('floor_number') }}" required>
                        @error('floor_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Room Number -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" 
                               value="{{ old('room_number') }}" required>
                        @error('room_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Map Section -->
                <div class="mb-4">
                    <label class="form-label">Location on Map</label>
                    <div id="map" style="height: 600px; border-radius: 8px; border: 1px solid #ddd;"></div>
                    <div id="location-info" class="mt-3 p-4 bg-white rounded shadow-sm" style="display: none;">
                        <div id="location-details" class="location-details"></div>
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Add Location
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
    <style>
        .location-edit {
            max-width: 1200px;
        }
        
        .card-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .is-invalid {
            border-color: #dc3545;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .btn-outline-secondary {
            border-color: #dee2e6;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
        }

        #map { 
            width: 100%; 
            height: 600px;
            margin-bottom: 10px;
            position: relative;
            overflow: hidden;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        #location-info {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .location-details {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .location-coordinates {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px;
        }

        .coordinate-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .coordinate-item:last-child {
            margin-bottom: 0;
        }

        .coordinate-label {
            color: #666;
            font-size: 13px;
        }

        .coordinate-value {
            color: #333;
            font-family: monospace;
            font-size: 13px;
            background-color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
            max-width: 70%;
            text-align: right;
            word-break: break-word;
        }

        .mapboxgl-marker {
            cursor: move;
        }

        .mapboxgl-marker:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        .click-animation {
            width: 20px;
            height: 20px;
            background-color: rgba(255, 0, 0, 0.6);
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            animation: pulse 1s ease-out;
            pointer-events: none;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.5);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .location-info {
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px;
            margin-top: 15px;
            display: none;
        }

        .location-coordinates {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .coordinate-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .coordinate-label {
            font-weight: 600;
            color: #4B5563;
            min-width: 80px;
        }

        .coordinate-value {
            color: #1F2937;
            font-family: monospace;
            background-color: #F3F4F6;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .mapboxgl-ctrl-geolocate {
            margin: 10px !important;
        }

        .mapboxgl-ctrl-group {
            margin: 10px !important;
        }

        .mapboxgl-canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Mapbox access token is available
            if (!'{{ config('services.mapbox.access_token') }}') {
                console.error('Mapbox access token is missing');
                document.getElementById('map').innerHTML = '<div class="alert alert-danger">Map cannot be loaded. Please check your Mapbox configuration.</div>';
                return;
            }

            // Initialize Mapbox
            mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';
            
            // Set default center coordinates
            var defaultCenter = [
                28.097913893018625,
                -25.540672986478395
            ];

            // Create the map
            var map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/satellite-streets-v12',
                center: defaultCenter,
                zoom: 17,
                pitch: 45,
                bearing: 0,
                attributionControl: false
            });

            // Add navigation controls
            map.addControl(new mapboxgl.NavigationControl({
                showCompass: true,
                showZoom: true,
                visualizePitch: true
            }), 'top-right');

            // Add geolocate control
            map.addControl(new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                trackUserLocation: true,
                showUserHeading: true
            }), 'top-right');

            // Add 3D building layer when the map loads
            map.on('load', function() {
                // Add 3D building layer with more subtle appearance
                map.addLayer({
                    'id': '3d-buildings',
                    'source': 'composite',
                    'source-layer': 'building',
                    'filter': ['==', 'extrude', 'true'],
                    'type': 'fill-extrusion',
                    'minzoom': 15,
                    'paint': {
                        'fill-extrusion-color': '#aaa',
                        'fill-extrusion-height': [
                            'interpolate',
                            ['linear'],
                            ['zoom'],
                            15,
                            0,
                            15.05,
                            ['get', 'height']
                        ],
                        'fill-extrusion-base': [
                            'interpolate',
                            ['linear'],
                            ['zoom'],
                            15,
                            0,
                            15.05,
                            ['get', 'min_height']
                        ],
                        'fill-extrusion-opacity': 0.4
                    }
                });

                // Add terrain with less exaggeration
                map.addSource('mapbox-dem', {
                    'type': 'raster-dem',
                    'url': 'mapbox://mapbox.mapbox-terrain-dem-v1',
                    'tileSize': 512,
                    'maxzoom': 14
                });
                map.setTerrain({ 'source': 'mapbox-dem', 'exaggeration': 1.2 });

                // Ensure map is properly loaded and centered
                map.resize();
                
                // Add a small delay before flying to ensure proper marker placement
                setTimeout(() => {
                    map.flyTo({
                        center: defaultCenter,
                        zoom: 17,
                        pitch: 45,
                        bearing: 0,
                        essential: true,
                        duration: 2000
                    });
                }, 100);
            });

            // Create a marker variable but don't add it to the map yet
            var marker = null;

            // Function to update location info display
            function updateLocationInfo(lngLat, address = null) {
                const locationInfo = document.getElementById('location-info');
                const locationDetails = document.getElementById('location-details');
                
                locationInfo.style.display = 'block';
                locationDetails.innerHTML = `
                    <div class="location-coordinates">
                        <div class="coordinate-item">
                            <span class="coordinate-label">Latitude:</span>
                            <span class="coordinate-value">${lngLat.lat.toFixed(6)}</span>
                        </div>
                        <div class="coordinate-item">
                            <span class="coordinate-label">Longitude:</span>
                            <span class="coordinate-value">${lngLat.lng.toFixed(6)}</span>
                        </div>
                        ${address ? `<div class="coordinate-item">
                            <span class="coordinate-label">Address:</span>
                            <span class="coordinate-value">${address}</span>
                        </div>` : ''}
                    </div>
                `;
            }

            // Function to show click animation
            function showClickAnimation(coords) {
                const el = document.createElement('div');
                el.className = 'click-animation';
                const animationMarker = new mapboxgl.Marker(el)
                    .setLngLat(coords)
                    .addTo(map);

                // Remove the animation marker after 1 second
                setTimeout(() => {
                    animationMarker.remove();
                }, 1000);
            }

            // Update location when map is clicked
            map.on('click', function(e) {
                // Show click animation
                showClickAnimation(e.lngLat);
                
                document.getElementById('longitude').value = e.lngLat.lng.toFixed(6);
                document.getElementById('latitude').value = e.lngLat.lat.toFixed(6);
                
                // Update location info
                updateLocationInfo(e.lngLat);

                // Try to get address using reverse geocoding
                fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${e.lngLat.lng},${e.lngLat.lat}.json?access_token=${mapboxgl.accessToken}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            const address = data.features[0].place_name;
                            updateLocationInfo(e.lngLat, address);
                        }
                    })
                    .catch(error => console.error('Error fetching address:', error));
            });

            // Set initial values
            document.getElementById('longitude').value = '';
            document.getElementById('latitude').value = '';

            // Handle map load errors
            map.on('error', function(e) {
                console.error('Map error:', e);
                document.getElementById('map').innerHTML = '<div class="alert alert-danger">Error loading map. Please try refreshing the page.</div>';
            });

            // Check for validation errors
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `
                        <ul class="text-left">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#3085d6'
                });
            @endif
        });
    </script>
@endpush
@endsection