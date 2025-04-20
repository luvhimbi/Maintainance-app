@extends('layouts.TechnicianNavBar')

@section('title', 'TUT South Campus Navigation')

@section('content')
<div class="container-fluid px-4 py-4 tut-campus-map">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i>
                            TUT South Campus Interactive Map
                        </h4>
                        <div>
                            <button id="track-location" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-location-arrow me-1"></i> Track My Location
                            </button>
                            <a href="{{ route('technician.dashboard') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Map Column -->
                        <div class="col-lg-8 position-relative">
                            <div id="map" style="height: 75vh; width: 100%;"></div>
                            <div id="location-info" class="position-absolute top-0 end-0 m-3 p-2 bg-white rounded shadow-sm d-none">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-circle text-success me-2"></i>
                                    <span id="location-status">Tracking active</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <span id="coordinates">0, 0</span> | 
                                    <span id="accuracy">Accuracy: 0m</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Panel -->
                        <div class="col-lg-4">
                            <div class="p-3" style="height: 75vh; overflow-y: auto;">
                                <div class="tut-brand mb-4 text-center">
                                    <img src="https://www.tut.ac.za/images/template/tut-logo.png" alt="TUT Logo" style="height: 50px;" class="mb-2">
                                    <h5 class="text-primary mb-2">Tshwane University of Technology</h5>
                                    <p class="small mb-0">2 Aubrey Matlakala St, Block K</p>
                                    <p class="small">Soshanguve South, 0152</p>
                                </div>

                                <div class="building-list mb-4">
                                    <h5 class="mb-3 text-primary">
                                        <i class="fas fa-building me-2"></i>Campus Buildings & Facilities
                                    </h5>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" id="building-search" class="form-control" placeholder="Search buildings...">
                                    </div>
                                    <div class="list-group building-list-container">
                                        @foreach($buildings as $building)
                                        <button type="button" 
                                                class="list-group-item list-group-item-action building-item"
                                                data-lat="{{ $building['position']['lat'] }}"
                                                data-lng="{{ $building['position']['lng'] }}"
                                                data-description="{{ $building['description'] ?? '' }}"
                                                data-departments="{{ isset($building['departments']) ? implode(', ', $building['departments']) : '' }}"
                                                data-facilities="{{ isset($building['facilities']) ? implode(', ', $building['facilities']) : '' }}">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $building['icon'] }}" width="24" height="24" class="me-2">
                                                {{ $building['name'] }}
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="directions-box">
                                    <h5 class="mb-3 text-primary">
                                        <i class="fas fa-route me-2"></i>Get Directions
                                    </h5>
                                    <div class="mb-3">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="origin-input" 
                                                   placeholder="Your location"
                                                   value="Current Location">
                                        </div>
                                        
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-flag-checkered"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="destination-input" 
                                                   placeholder="Select building"
                                                   readonly>
                                        </div>
                                        
                                        <select id="travel-mode" class="form-select">
                                            <option value="WALKING">Walking</option>
                                            <option value="DRIVING">Driving</option>
                                            <option value="BICYCLING">Bicycling</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex gap-2 mb-3">
                                        <button id="get-directions" class="btn btn-primary flex-grow-1">
                                            <i class="fas fa-directions me-2"></i>Show Route
                                        </button>
                                        <button id="clear-route" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    
                                    <div id="directions-panel" class="bg-light p-3 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Google Maps API -->
<script>
    let map, directionsService, directionsRenderer;
    let markers = [];
    let infoWindows = [];
    let selectedBuilding = null;
    let userMarker = null;
    let watchId = null;
    let geocoder;
    let isTracking = false;

    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    window.initMap = function() {
        try {
            // Initialize map with custom style
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: { 
                    lat: {{ $defaultLocation['lat'] }}, 
                    lng: {{ $defaultLocation['lng'] }}
                },
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                    mapTypeIds: ['roadmap', 'satellite', 'hybrid']
                },
                styles: [
                    {
                        "featureType": "poi",
                        "stylers": [
                            { "visibility": "off" }
                        ]
                    }
                ]
            });

            // Initialize geocoder
            geocoder = new google.maps.Geocoder();

            // Add campus buildings markers with enhanced info windows
            @foreach($buildings as $building)
            (function() {
                const marker = new google.maps.Marker({
                    position: { 
                        lat: {{ $building['position']['lat'] }}, 
                        lng: {{ $building['position']['lng'] }}
                    },
                    map: map,
                    title: "{{ $building['name'] }}",
                    icon: {
                        url: "{{ $building['icon'] }}",
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                // Build info window content
                let content = `
                    <div class="map-info-window">
                        <h6 class="mb-1">{{ $building['name'] }}</h6>
                        <p class="small mb-2">{{ $building['description'] ?? '' }}</p>`;
                
                @if(isset($building['departments']))
                content += `
                    <div class="departments mb-2">
                        <strong><i class="fas fa-door-open me-1"></i>Departments:</strong>
                        <ul class="small mb-0 ps-3">`;
                @foreach($building['departments'] as $dept)
                content += `<li>{{ $dept }}</li>`;
                @endforeach
                content += `</ul></div>`;
                @endif
                
                @if(isset($building['facilities']))
                content += `
                    <div class="facilities">
                        <strong><i class="fas fa-restroom me-1"></i>Facilities:</strong>
                        <ul class="small mb-0 ps-3">`;
                @foreach($building['facilities'] as $facility)
                content += `<li>{{ $facility }}</li>`;
                @endforeach
                content += `</ul></div>`;
                @endif
                
                content += `</div>`;

                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: content
                });

                marker.addListener('click', () => {
                    // Close all other info windows
                    infoWindows.forEach(iw => iw.close());
                    infoWindow.open(map, marker);
                    
                    // Update selected building
                    document.querySelectorAll('.building-item').forEach(el => el.classList.remove('active'));
                    document.querySelector(`.building-item[data-lat="{{ $building['position']['lat'] }}"][data-lng="{{ $building['position']['lng'] }}"]`).classList.add('active');
                    document.getElementById('destination-input').value = "{{ $building['name'] }}";
                    selectedBuilding = "{{ $building['name'] }}";
                });

                markers.push(marker);
                infoWindows.push(infoWindow);
            })();
            @endforeach

            // Initialize directions services
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
                polylineOptions: {
                    strokeColor: '#3a86ff',
                    strokeWeight: 5,
                    strokeOpacity: 0.8
                },
                panel: document.getElementById('directions-panel')
            });
            directionsRenderer.setMap(map);

            // Setup building selection
            document.querySelectorAll('.building-item').forEach(item => {
                item.addEventListener('click', function() {
                    // Close all info windows
                    infoWindows.forEach(iw => iw.close());
                    
                    // Remove active class from all items
                    document.querySelectorAll('.building-item').forEach(el => el.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);
                    selectedBuilding = this.textContent.trim();
                    document.getElementById('destination-input').value = selectedBuilding;
                    
                    // Center map on selected building
                    map.panTo({ lat, lng });
                    
                    // Open marker's info window
                    markers.forEach(marker => {
                        if (marker.getPosition().lat() === lat && marker.getPosition().lng() === lng) {
                            infoWindows[markers.indexOf(marker)].open(map, marker);
                        }
                    });
                });
            });

            // Setup directions functionality
            document.getElementById('get-directions').addEventListener('click', calculateRoute);
            document.getElementById('clear-route').addEventListener('click', clearRoute);
            
            // Building search functionality
            document.getElementById('building-search').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.building-item').forEach(item => {
                    const buildingName = item.textContent.toLowerCase();
                    if (buildingName.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Initialize autocomplete for origin input
            const originInput = document.getElementById('origin-input');
            const autocomplete = new google.maps.places.Autocomplete(originInput, {
                types: ['geocode'],
                componentRestrictions: { country: 'za' }
            });
            autocomplete.bindTo('bounds', map);

            // Setup geolocation tracking
            document.getElementById('track-location').addEventListener('click', toggleTracking);

        } catch (error) {
            console.error('Map Error:', error);
            alert('Failed to load campus map. Please try again.');
        }
    };

    function toggleTracking() {
        if (isTracking) {
            stopTracking();
        } else {
            startTracking();
        }
    }

    function startTracking() {
        if (navigator.geolocation) {
            isTracking = true;
            document.getElementById('track-location').innerHTML = '<i class="fas fa-stop me-1"></i> Stop Tracking';
            document.getElementById('track-location').classList.remove('btn-light');
            document.getElementById('track-location').classList.add('btn-danger');
            document.getElementById('location-info').classList.remove('d-none');

            // Create user marker if it doesn't exist
            if (!userMarker) {
                userMarker = new google.maps.Marker({
                    position: map.getCenter(),
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#4285F4',
                        fillOpacity: 1,
                        strokeColor: '#FFFFFF',
                        strokeWeight: 2
                    },
                    title: 'Your Location'
                });
            }

            watchId = navigator.geolocation.watchPosition(
                position => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Update marker position
                    userMarker.setPosition(pos);
                    
                    // Update location info display
                    document.getElementById('coordinates').textContent = 
                        `${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                    document.getElementById('accuracy').textContent = 
                        `Accuracy: ${Math.round(position.coords.accuracy)}m`;
                    
                    // Center map on user if zoomed out
                    if (map.getZoom() < 18) {
                        map.panTo(pos);
                    }
                    
                    // Update origin input if it's set to current location
                    if (document.getElementById('origin-input').value === 'Current Location') {
                        geocoder.geocode({ location: pos }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                document.getElementById('origin-input').value = results[0].formatted_address;
                            }
                        });
                    }
                },
                error => {
                    console.error('Geolocation error:', error);
                    document.getElementById('location-status').textContent = 'Tracking error';
                    document.getElementById('location-status').previousElementSibling.className = 'fas fa-circle text-danger me-2';
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 5000
                }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }

    function stopTracking() {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        isTracking = false;
        document.getElementById('track-location').innerHTML = '<i class="fas fa-location-arrow me-1"></i> Track My Location';
        document.getElementById('track-location').classList.remove('btn-danger');
        document.getElementById('track-location').classList.add('btn-light');
        document.getElementById('location-info').classList.add('d-none');
    }

    async function calculateRoute() {
        const origin = document.getElementById('origin-input').value;
        const destination = document.getElementById('destination-input').value;
        const travelMode = document.getElementById('travel-mode').value;

        if (!destination) {
            alert('Please select a building from the list');
            return;
        }

        try {
            const originLocation = await getLocation(origin);
            const destinationLocation = await getLocation(destination);

            // Add campus entrance as a waypoint for better navigation
            const waypoints = [];
            if (selectedBuilding && selectedBuilding !== 'Main Entrance') {
                waypoints.push({
                    location: new google.maps.LatLng({{ $defaultLocation['lat'] }}, {{ $defaultLocation['lng'] }}),
                    stopover: true
                });
            }

            directionsService.route({
                origin: originLocation,
                destination: destinationLocation,
                waypoints: waypoints,
                travelMode: travelMode,
                provideRouteAlternatives: false,
                optimizeWaypoints: true
            }, (response, status) => {
                if (status === 'OK') {
                    directionsRenderer.setDirections(response);
                } else {
                    alert('Directions request failed: ' + status);
                }
            });
        } catch (error) {
            alert('Error calculating route: ' + error.message);
        }
    }

    function clearRoute() {
        directionsRenderer.setDirections({ routes: [] });
        document.getElementById('directions-panel').innerHTML = '';
        document.getElementById('origin-input').value = 'Current Location';
        document.getElementById('destination-input').value = '';
        document.querySelectorAll('.building-item').forEach(el => el.classList.remove('active'));
    }

    function getLocation(input) {
        return new Promise((resolve, reject) => {
            if (input === 'Current Location') {
                if (userMarker && isTracking) {
                    resolve(userMarker.getPosition());
                } else if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => resolve({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        }),
                        () => {
                            alert('Could not get your current location. Defaulting to campus entrance.');
                            resolve({ lat: {{ $defaultLocation['lat'] }}, lng: {{ $defaultLocation['lng'] }} });
                        },
                        { timeout: 5000 }
                    );
                } else {
                    alert('Geolocation is not supported by your browser. Defaulting to campus entrance.');
                    resolve({ lat: {{ $defaultLocation['lat'] }}, lng: {{ $defaultLocation['lng'] }} });
                }
            } else {
                new google.maps.Geocoder().geocode(
                    { address: input },
                    (results, status) => {
                        if (status === 'OK') {
                            resolve(results[0].geometry.location);
                        } else {
                            reject(new Error('Location not found'));
                        }
                    }
                );
            }
        });
    }

    // Clean up geolocation tracking when page unloads
    window.addEventListener('beforeunload', function() {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
        }
    });

    document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
</script>

<style>
.tut-campus-map {
    background: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card {
    border: none;
    border-radius: 0.5rem;
    overflow: hidden;
}

.card-header {
    border-radius: 0;
    padding: 1rem 1.5rem;
}

.building-item {
    border: none;
    border-bottom: 1px solid #dee2e6;
    text-align: left;
    transition: all 0.2s;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem !important;
    margin-bottom: 0.25rem;
}

.building-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.building-item.active {
    background-color: #003366;
    color: white;
}

#directions-panel {
    max-height: 300px;
    overflow-y: auto;
    font-size: 0.9rem;
}

.adp-step {
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.adp-step-title {
    font-weight: bold;
    color: #003366;
}

.adp-directions {
    color: #555;
}

.input-group-text {
    background: #003366;
    color: white;
    border-color: #002244;
}

.btn-primary {
    background: #003366;
    border-color: #002244;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: #002244;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.btn-danger {
    transition: all 0.3s;
}

/* Info window styles */
.map-info-window {
    max-width: 250px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.map-info-window h6 {
    color: #003366;
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.map-info-window .small {
    font-size: 0.8rem;
    color: #555;
    line-height: 1.4;
}

.map-info-window ul {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
}

.map-info-window li {
    margin-bottom: 0.2rem;
    font-size: 0.8rem;
}

/* Location info box */
#location-info {
    z-index: 1000;
    max-width: 200px;
    font-size: 0.85rem;
}

/* Building search */
#building-search {
    border-radius: 0.25rem !important;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .col-lg-8, .col-lg-4 {
        width: 100%;
    }
    
    #map {
        height: 50vh !important;
    }
    
    .card-body > .row > div {
        height: auto !important;
    }
    
    #location-info {
        top: 60px !important;
        right: 10px !important;
    }
}
</style>
@endsection