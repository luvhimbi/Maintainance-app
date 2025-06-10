@extends('Layouts.AdminNavBar')
@section('title', 'Edit Location')
@section('content')
<div class="container-fluid location-edit py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div>
                <h2 class="h5 mb-1">Edit Location</h2>
                <p class="text-muted small mb-0">Update the details for this location</p>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body px-4">
            <form action="{{ route('admin.locations.update', $location->location_id) }}" method="POST" id="editLocationForm">
                @csrf
                @method('PUT')
                <div class="row mb-4">
                    <!-- Building Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Building Name <span class="text-danger">*</span></label>
                        <input type="text" name="building_name" class="form-control @error('building_name') is-invalid @enderror" 
                               value="{{ old('building_name', $location->building_name) }}" required>
                        @error('building_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Floor Number -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Floor Number <span class="text-danger">*</span></label>
                        <input type="text" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror" 
                               value="{{ old('floor_number', $location->floor_number) }}" required>
                        @error('floor_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Room Number -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" 
                               value="{{ old('room_number', $location->room_number) }}" required>
                        @error('room_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description', $location->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Map Section -->
                <div class="mb-4">
                    <label class="form-label">Location on Map</label>
                    <div id="map" style="height: 600px; border-radius: 8px; border: 1px solid #ddd;"></div>
                    <div id="location-info" class="mt-3 p-4 bg-white rounded shadow-sm" style="display: none;">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <h6 class="mb-0">Selected Location</h6>
                        </div>
                        <div id="location-details" class="location-details"></div>
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" required>
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" required>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Location
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

        #location-info h6 {
            color: #2c3e50;
            font-weight: 600;
        }

        .location-details {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .location-details .address {
            color: #2c3e50;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .location-details .coordinates {
            color: #6c757d;
            font-size: 0.9rem;
            font-family: monospace;
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            display: inline-block;
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
            
            // Set initial coordinates from the location
            var initialCenter = [
                {{ $location->longitude ?? 28.097913893018625 }},
                {{ $location->latitude ?? -25.540672986478395 }}
            ];

            // Create the map
            var map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/satellite-streets-v12',
                center: initialCenter,
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
                        center: initialCenter,
                        zoom: 17,
                        pitch: 45,
                        bearing: 0,
                        essential: true,
                        duration: 2000
                    });
                }, 100);
            });

            // Create a marker with improved positioning
            var marker = new mapboxgl.Marker({
                draggable: true,
                color: '#FF0000',
                anchor: 'bottom'
            })
            .setLngLat(initialCenter)
            .addTo(map);

            // Function to update location info display
            function updateLocationInfo(lngLat, address = null) {
                const locationInfo = document.getElementById('location-info');
                const locationDetails = document.getElementById('location-details');
                
                locationInfo.style.display = 'block';
                if (address) {
                    locationDetails.innerHTML = `
                        <div class="address">${address}</div>
                        <div class="coordinates">${lngLat.lat.toFixed(6)}, ${lngLat.lng.toFixed(6)}</div>
                    `;
                } else {
                    locationDetails.innerHTML = `
                        <div class="coordinates">${lngLat.lat.toFixed(6)}, ${lngLat.lng.toFixed(6)}</div>
                    `;
                }
            }

            // Update marker position when map is clicked
            map.on('click', function(e) {
                marker.setLngLat(e.lngLat);
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

            // Update coordinates when marker is dragged
            function onDragEnd() {
                var lngLat = marker.getLngLat();
                document.getElementById('longitude').value = lngLat.lng.toFixed(6);
                document.getElementById('latitude').value = lngLat.lat.toFixed(6);
                
                // Update location info
                updateLocationInfo(lngLat);

                // Try to get address using reverse geocoding
                fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${lngLat.lng},${lngLat.lat}.json?access_token=${mapboxgl.accessToken}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            const address = data.features[0].place_name;
                            updateLocationInfo(lngLat, address);
                        }
                    })
                    .catch(error => console.error('Error fetching address:', error));
            }

            marker.on('dragend', onDragEnd);

            // Set initial values and location info
            document.getElementById('longitude').value = initialCenter[0].toFixed(6);
            document.getElementById('latitude').value = initialCenter[1].toFixed(6);
            updateLocationInfo({ lat: initialCenter[1], lng: initialCenter[0] });

            // Handle map load errors
            map.on('error', function(e) {
                console.error('Map error:', e);
                document.getElementById('map').innerHTML = '<div class="alert alert-danger">Error loading map. Please try refreshing the page.</div>';
            });

            // Check for success message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    timer: 3000
                });
            @endif

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