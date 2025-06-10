@extends('layouts.TechnicianNavBar')

@section('title', 'Smart Navigation')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i>
                           Smart Nav
                        </h4>
                        <a href="{{ route('technician.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Map Column -->
                        <div class="col-lg-9 position-relative">
                            <div id="map" style="height: 85vh; width: 100%;"></div>
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

                            <!-- Navigation UI -->
                            <div id="navigation-ui" class="position-absolute bottom-0 start-0 end-0 p-3 bg-white shadow-lg d-none">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div id="next-maneuver-icon" class="text-primary fs-4">
                                            <i class="fas fa-arrow-up"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div id="next-maneuver-text" class="fw-bold">Continue straight</div>
                                        <div id="next-maneuver-distance" class="small text-muted">in 100 meters</div>
                                    </div>
                                    <div class="col-auto">
                                        <button id="toggle-voice" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-volume-up"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div id="route-progress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <!-- Add route summary -->
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <div class="text-primary">
                                        <i class="fas fa-road me-1"></i>
                                        <span id="total-distance">0 km</span>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-clock me-1"></i>
                                        <span id="total-duration">0 min</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Permission Alert -->
                            <div id="location-permission-alert" class="position-absolute top-50 start-50 translate-middle bg-white p-4 rounded shadow-lg d-none" style="z-index: 1000;">
                                <div class="text-center mb-3">
                                    <i class="fas fa-map-marker-alt text-primary fa-3x"></i>
                                </div>
                                <h5 class="text-center mb-3">Location Access Required</h5>
                                <p class="text-center mb-3">Please enable location access to use your current location.</p>
                                <div class="d-grid">
                                    <button id="request-location" class="btn btn-primary">
                                        <i class="fas fa-location-arrow me-2"></i>Enable Location
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Controls Panel -->
                        <div class="col-lg-3">
                            <div class="p-3" style="height: 85vh; overflow-y: auto;">
                                <div class="navigation-controls">
                                    <div class="mb-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-directions me-2"></i>
                                            Navigation Controls
                                        </h5>

                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control"
                                                   id="start-location-input"
                                                   placeholder="Enter start location">
                                        </div>
                                        <div id="start-location-results" class="list-group mb-3 d-none" style="max-height: 200px; overflow-y: auto;">
                                        </div>

                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-flag-checkered"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control"
                                                   id="destination-input"
                                                   placeholder="Enter destination">
                                        </div>
                                        <div id="destination-results" class="list-group mb-3 d-none" style="max-height: 200px; overflow-y: auto;">
                                        </div>

                                        <!-- All Locations List -->
                                        <div class="mb-3">
                                            <h6 class="text-primary mb-2">
                                                <i class="fas fa-list me-2"></i>
                                               Campus Locations
                                            </h6>
                                            <div id="all-locations-list" class="list-group" style="max-height: 300px; overflow-y: auto;">
                                                <!-- Locations will be loaded here -->
                                            </div>
                                        </div>

                                        <!-- Location Buttons -->
                                        <div class="mb-3">
                                            <div class="d-grid gap-2">
                                                <button id="get-user-location" class="btn btn-outline-primary">
                                                    <i class="fas fa-location-arrow me-2"></i>Use My Location
                                                </button>
                                            </div>
                                        </div>

                                        <select id="travel-mode" class="form-select mb-3">
                                            <option value="walking">Walking</option>
                                            <option value="driving">Driving</option>
                                            <option value="cycling">Cycling</option>
                                        </select>

                                        <div class="d-grid gap-2">
                                            <button id="start-navigation" class="btn btn-primary">
                                                <i class="fas fa-play me-2"></i> Start Navigation
                                            </button>
                                            <button id="clear-route" class="btn btn-outline-danger">
                                                <i class="fas fa-times me-2"></i> Clear Route
                                            </button>
                                        </div>
                                    </div>

                                    <div id="navigation-instructions" class="mt-4 p-3 bg-light rounded d-none">
                                        <h6><i class="fas fa-list-ol me-2"></i> Turn-by-Turn Directions</h6>
                                        <div id="steps-container" class="mt-2"></div>
                                    </div>

                                    <div id="route-info" class="mt-4 p-3 bg-light rounded d-none">
                                        <!-- Route summary will be displayed here -->
                                    </div>

                                    <div id="loading-indicator" class="text-center mt-4 d-none">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 small text-muted">Calculating route...</p>
                                    </div>

                                    <div id="navigation-error" class="alert alert-danger mt-4 d-none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
mapboxgl.accessToken = '{{ $mapboxAccessToken }}';
let map, userLocation;
let markers = [];
let watchId = null;
let currentRoute = null;
let navigationActive = false;
let voiceEnabled = true;
let routeProgress = 0;
let currentStepIndex = 0;
let speechSynthesis = window.speechSynthesis;
let userLocationMarker = null;
let geolocateControl = null;
let startLocationMarker = null;
let endLocationMarker = null;

// Initialize Mapbox map
function initMap() {
    try {
        // Get location parameters from URL
        const urlParams = new URLSearchParams(window.location.search);
        const building = urlParams.get('building');
        const room = urlParams.get('room');
        const lat = urlParams.get('lat');
        const lng = urlParams.get('lng');

        // Set initial center based on URL parameters or default
        const initialCenter = lat && lng ? 
            [parseFloat(lng), parseFloat(lat)] : 
            [28.098271679102634, -25.53978422415537];

        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: initialCenter,
            zoom: 17,
            pitch: 45,
            bearing: 0
        });

        // Add navigation controls
        map.addControl(new mapboxgl.NavigationControl());

        // Setup geolocation control
        geolocateControl = new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true,
            showAccuracyCircle: true
        });
        map.addControl(geolocateControl);

        // Add click event listener for selecting points
        map.on('click', handleMapClick);

        // Wait for map to load before adding event listeners and custom layers
        map.on('load', () => {
            console.log('Map loaded successfully');
            setupEventListeners();
            enhanceMapVisibility();

            // If location parameters exist, create destination marker
            if (building && lat && lng) {
                // Create destination marker
                const el = document.createElement('div');
                el.className = 'location-marker';
                el.innerHTML = `
                    <div class="location-marker-content">
                        <i class="fas fa-flag"></i>
                        <div class="location-marker-tooltip">${building}${room ? `, Room ${room}` : ''}</div>
                    </div>
                `;

                // Remove existing end marker if any
                if (endLocationMarker) {
                    endLocationMarker.remove();
                }

                // Add new marker
                endLocationMarker = new mapboxgl.Marker(el)
                    .setLngLat([parseFloat(lng), parseFloat(lat)])
                    .addTo(map);

                // Update destination input
                document.getElementById('destination-input').value = `${building}${room ? `, Room ${room}` : ''}`;

                // Add a popup with location information
                const popup = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`
                        <div class="p-2">
                            <h6 class="mb-1">${building}</h6>
                            ${room ? `<p class="mb-0 text-muted">Room ${room}</p>` : ''}
                        </div>
                    `);

                endLocationMarker.setPopup(popup);

                // Center map on destination with animation
                map.flyTo({
                    center: [parseFloat(lng), parseFloat(lat)],
                    zoom: 17,
                    essential: true,
                    duration: 2000
                });
            }
        });

    } catch (error) {
        console.error('Map initialization failed:', error);
        showLocationError('Failed to initialize map. Please check your connection.');
    }
}

// Function to enhance map visibility
function enhanceMapVisibility() {
    // Add 3D buildings layer
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
            'fill-extrusion-opacity': 0.6
        }
    });

    // Enhance roads visibility
    map.addLayer({
        'id': 'road-labels',
        'type': 'symbol',
        'source': 'composite',
        'source-layer': 'road',
        'layout': {
            'text-field': ['get', 'name'],
            'text-size': 12,
            'text-anchor': 'top',
            'text-offset': [0, 1]
        },
        'paint': {
            'text-color': '#000',
            'text-halo-color': '#fff',
            'text-halo-width': 1
        }
    });

    // Add custom style for pathways
    map.addLayer({
        'id': 'pathways',
        'type': 'line',
        'source': 'composite',
        'source-layer': 'road',
        'filter': ['==', ['get', 'class'], 'path'],
        'layout': {
            'line-join': 'round',
            'line-cap': 'round'
        },
        'paint': {
            'line-color': '#ff0000',
            'line-width': 2,
            'line-opacity': 0.8
        }
    });

    // Add custom style for roads
    map.addLayer({
        'id': 'roads',
        'type': 'line',
        'source': 'composite',
        'source-layer': 'road',
        'filter': ['!=', ['get', 'class'], 'path'],
        'layout': {
            'line-join': 'round',
            'line-cap': 'round'
        },
        'paint': {
            'line-color': '#000',
            'line-width': 3,
            'line-opacity': 0.8
        }
    });

    // Add building labels
    map.addLayer({
        'id': 'building-labels',
        'type': 'symbol',
        'source': 'composite',
        'source-layer': 'building',
        'layout': {
            'text-field': ['get', 'name'],
            'text-size': 12,
            'text-anchor': 'center',
            'text-offset': [0, 0]
        },
        'paint': {
            'text-color': '#000',
            'text-halo-color': '#fff',
            'text-halo-width': 1
        }
    });
}

function requestLocationPermission() {
    const locationAlert = document.getElementById('location-permission-alert');
    const requestButton = document.getElementById('request-location');

    if (locationAlert) {
        locationAlert.classList.remove('d-none');
    }

    if (requestButton) {
        requestButton.addEventListener('click', () => {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Success
                    if (locationAlert) {
                        locationAlert.classList.add('d-none');
                    }
                    handleGeolocation(position);
                    startLocationTracking();
                },
                (error) => {
                    // Error
                    handleGeolocationError(error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
}

function startLocationTracking() {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
    }

    const options = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    };

    watchId = navigator.geolocation.watchPosition(
        (position) => {
            updateUserLocation(position);
            // Show location info
            const locationInfo = document.getElementById('location-info');
            if (locationInfo) {
                locationInfo.classList.remove('d-none');
            }
        },
        (error) => {
            handleGeolocationError(error);
        },
        options
    );
}

function showLocationError(message) {
    const errorDisplay = document.getElementById('navigation-error');
    if (errorDisplay) {
        errorDisplay.textContent = message;
        errorDisplay.classList.remove('d-none');
    }
}

// Event handlers
function setupEventListeners() {
    const startBtn = document.getElementById('start-navigation');
    const clearBtn = document.getElementById('clear-route');
    const useMyLocationBtn = document.getElementById('get-user-location');
    const toggleVoiceBtn = document.getElementById('toggle-voice');

    if (startBtn && clearBtn) {
        startBtn.addEventListener('click', startNavigation);
        clearBtn.addEventListener('click', clearRoute);
    }

    if (useMyLocationBtn) {
        useMyLocationBtn.addEventListener('click', getUserLocation);
    }

    if (toggleVoiceBtn) {
        toggleVoiceBtn.addEventListener('click', toggleVoiceGuidance);
    }

    // Add geolocate control event listeners
    if (geolocateControl) {
        geolocateControl.on('geolocate', handleGeolocation);
        geolocateControl.on('error', handleGeolocationError);
    }
}

function toggleVoiceGuidance() {
    voiceEnabled = !voiceEnabled;
    const icon = document.querySelector('#toggle-voice i');
    if (voiceEnabled) {
        icon.className = 'fas fa-volume-up';
        speak('Voice guidance enabled');
    } else {
        icon.className = 'fas fa-volume-mute';
        speak('Voice guidance disabled');
    }
}

function speak(text) {
    if (!voiceEnabled) return;

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.rate = 1;
    utterance.pitch = 1;
    utterance.volume = 1;
    speechSynthesis.speak(utterance);
}

// Update user location
function updateUserLocation(position) {
    const { latitude, longitude, accuracy } = position.coords;
    userLocation = [longitude, latitude];

    // Update location display
    updateLocationDisplay(position.coords);

    // Add or update marker
    if (!userLocationMarker) {
        // Create a pulsing dot for user location
        const el = document.createElement('div');
        el.className = 'user-location-marker';
        el.style.width = '20px';
        el.style.height = '20px';
        el.style.borderRadius = '50%';
        el.style.backgroundColor = '#4285F4';
        el.style.border = '2px solid white';
        el.style.boxShadow = '0 0 0 2px rgba(66, 133, 244, 0.5)';
        el.style.animation = 'pulse 1.5s infinite';

        // Add style for the animation
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes pulse {
                0% { box-shadow: 0 0 0 0 rgba(66, 133, 244, 0.5); }
                70% { box-shadow: 0 0 0 15px rgba(66, 133, 244, 0); }
                100% { box-shadow: 0 0 0 0 rgba(66, 133, 244, 0); }
            }
        `;
        document.head.appendChild(style);

        userLocationMarker = new mapboxgl.Marker(el)
            .setLngLat(userLocation)
            .addTo(map);

        // Center map on first location acquisition
        map.flyTo({ center: userLocation, zoom: 15 });
    } else {
        userLocationMarker.setLngLat(userLocation);
    }

    // Add accuracy circle
    updateAccuracyCircle(userLocation, accuracy);
}

// Show accuracy radius
function updateAccuracyCircle(center, accuracyMeters) {
    // Remove previous accuracy circle
    if (map.getSource('accuracy-circle')) {
        map.removeLayer('accuracy-circle-fill');
        map.removeLayer('accuracy-circle-border');
        map.removeSource('accuracy-circle');
    }

    // Create a circle representing accuracy
    const accuracyCircle = turf.circle(center, accuracyMeters / 1000, { units: 'kilometers' });

    map.addSource('accuracy-circle', {
        'type': 'geojson',
        'data': accuracyCircle
    });

    map.addLayer({
        'id': 'accuracy-circle-fill',
        'type': 'fill',
        'source': 'accuracy-circle',
        'paint': {
            'fill-color': '#4285F4',
            'fill-opacity': 0.1
        }
    });

    map.addLayer({
        'id': 'accuracy-circle-border',
        'type': 'line',
        'source': 'accuracy-circle',
        'paint': {
            'line-color': '#4285F4',
            'line-width': 1,
            'line-opacity': 0.5
        }
    });
}

// Handle geolocation errors
function handleGeolocationError(error) {
    let errorMessage;

    switch(error.code) {
        case error.PERMISSION_DENIED:
            errorMessage = "Location access denied. Please check your browser permissions.";
            break;
        case error.POSITION_UNAVAILABLE:
            errorMessage = "Location information is unavailable.";
            break;
        case error.TIMEOUT:
            errorMessage = "Location request timed out.";
            break;
        default:
            errorMessage = "An unknown error occurred getting your location.";
    }

    showLocationError(errorMessage);

    const locationStatus = document.getElementById('location-status');
    if (locationStatus) {
        locationStatus.textContent = 'Location tracking failed';
        locationStatus.parentElement.classList.remove('text-success');
        locationStatus.parentElement.classList.add('text-danger');
    }

    watchId = null;
}

function handleGeolocation(e) {
    console.log('Location acquired:', e.coords);
    userLocation = [e.coords.longitude, e.coords.latitude];
    updateLocationDisplay(e.coords);
}

function updateLocationDisplay(coords) {
    const locationInfo = document.getElementById('location-info');
    const coordinates = document.getElementById('coordinates');
    const accuracy = document.getElementById('accuracy');
    const locationStatus = document.getElementById('location-status');

    if (locationInfo && coordinates && accuracy) {
        coordinates.textContent = `${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)}`;
        accuracy.textContent = `Accuracy: ${Math.round(coords.accuracy)}m`;
        locationInfo.classList.remove('d-none');
    }

    if (locationStatus) {
        locationStatus.textContent = 'Location tracking enabled';
        locationStatus.classList.add('text-success');
        locationStatus.classList.remove('text-danger');
    }
}

// Function to handle map clicks
function handleMapClick(e) {
    const coords = e.lngLat;
    console.log('Map clicked at:', coords);

    // If no start location is set, set it as start location
    if (!startLocationMarker) {
        const el = document.createElement('div');
        el.className = 'location-marker';
        el.innerHTML = `
            <div class="location-marker-content">
                <i class="fas fa-map-marker-alt"></i>
                <div class="location-marker-tooltip">Start Location</div>
            </div>
        `;

        startLocationMarker = new mapboxgl.Marker(el)
            .setLngLat(coords)
            .addTo(map);

        // Update start location input
        document.getElementById('start-location-input').value = 'Selected Location';
    } 
    // If start location exists but no destination, set as destination
    else if (!endLocationMarker) {
        const el = document.createElement('div');
        el.className = 'location-marker';
        el.innerHTML = `
            <div class="location-marker-content">
                <i class="fas fa-flag"></i>
                <div class="location-marker-tooltip">Destination</div>
            </div>
        `;

        endLocationMarker = new mapboxgl.Marker(el)
            .setLngLat(coords)
            .addTo(map);

        // Update destination input
        document.getElementById('destination-input').value = 'Selected Location';

        // Calculate route if both points are set
        calculateRoute({
            origin: startLocationMarker.getLngLat().toArray(),
            destination: endLocationMarker.getLngLat().toArray(),
            profile: document.getElementById('travel-mode').value
        }).then(routeData => {
            displayRoute(routeData);
        }).catch(error => {
            console.error('Route calculation error:', error);
            handleNavigationError(error);
        });
    }
}

// Function to get user location
function getUserLocation() {
    if (navigator.geolocation) {
        // Show location permission alert
        const locationAlert = document.getElementById('location-permission-alert');
        if (locationAlert) {
            locationAlert.classList.remove('d-none');
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                // Success
                if (locationAlert) {
                    locationAlert.classList.add('d-none');
                }

                const { latitude, longitude } = position.coords;
                
                // Create marker for user location
                const el = document.createElement('div');
                el.className = 'location-marker';
                el.innerHTML = `
                    <div class="location-marker-content">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="location-marker-tooltip">My Location</div>
                    </div>
                `;

                // Remove existing start marker if any
                if (startLocationMarker) {
                    startLocationMarker.remove();
                }

                // Add new marker
                startLocationMarker = new mapboxgl.Marker(el)
                    .setLngLat([longitude, latitude])
                    .addTo(map);

                // Update start location input
                document.getElementById('start-location-input').value = 'My Location';

                // Center map on user's location
                map.flyTo({
                    center: [longitude, latitude],
                    zoom: 17,
                    essential: true
                });

                // Start location tracking
                startLocationTracking();
            },
            (error) => {
                // Error
                if (locationAlert) {
                    locationAlert.classList.add('d-none');
                }
                handleGeolocationError(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        showLocationError('Geolocation is not supported by your browser');
    }
}

// Function to start navigation
async function startNavigation() {
    console.log('Starting navigation');
    const startInput = document.getElementById('start-location-input');
    const destinationInput = document.getElementById('destination-input');
    const profileSelect = document.getElementById('travel-mode');
    const errorDisplay = document.getElementById('navigation-error');
    const loadingIndicator = document.getElementById('loading-indicator');

    try {
        if (errorDisplay) errorDisplay.classList.add('d-none');
        if (loadingIndicator) loadingIndicator.classList.remove('d-none');

        let originCoords, destinationCoords;

        // Get origin coordinates
        if (startLocationMarker) {
            originCoords = startLocationMarker.getLngLat().toArray();
        } else if (userLocation) {
            originCoords = userLocation;
        } else {
            throw new Error('Please set a start location or use your current location');
        }

        // Get destination coordinates
        if (endLocationMarker) {
            destinationCoords = endLocationMarker.getLngLat().toArray();
        } else {
            throw new Error('Please set a destination location');
        }

        // Remove existing route layer
        if (map.getSource('route')) {
            map.removeLayer('route-glow');
            map.removeLayer('route');
            map.removeSource('route');
        }

        // Calculate route
        const routeData = await calculateRoute({
            origin: originCoords,
            destination: destinationCoords,
            profile: profileSelect ? profileSelect.value : 'walking'
        });

        // Store current route
        currentRoute = routeData.routes[0];
        navigationActive = true;
        currentStepIndex = 0;

        // Display results
        displayRoute(routeData);
        showInstructions(routeData.routes[0].legs[0].steps);

        // Show navigation UI
        document.getElementById('navigation-ui').classList.remove('d-none');

        // Start navigation updates
        startNavigationUpdates();

        // Ensure markers are visible
        if (startLocationMarker) {
            startLocationMarker.getElement().style.zIndex = '1000';
        }
        if (endLocationMarker) {
            endLocationMarker.getElement().style.zIndex = '1000';
        }

    } catch (error) {
        console.error('Navigation error:', error);
        handleNavigationError(error, errorDisplay);
    } finally {
        if (loadingIndicator) loadingIndicator.classList.add('d-none');
    }
}

function startNavigationUpdates() {
    if (!navigationActive || !currentRoute) return;

    // Update navigation UI every second
    const updateInterval = setInterval(() => {
        if (!navigationActive) {
            clearInterval(updateInterval);
            return;
        }

        updateNavigationUI();
    }, 1000);
}

function updateNavigationUI() {
    if (!currentRoute || !userLocation) return;

    const steps = currentRoute.legs[0].steps;
    const currentStep = steps[currentStepIndex];

    // Calculate progress
    const totalDistance = currentRoute.distance;
    const remainingDistance = calculateRemainingDistance();
    routeProgress = ((totalDistance - remainingDistance) / totalDistance) * 100;

    // Update progress bar
    document.getElementById('route-progress').style.width = `${routeProgress}%`;

    // Update next maneuver
    const nextManeuver = currentStep.maneuver;
    const nextManeuverIcon = document.getElementById('next-maneuver-icon');
    const nextManeuverText = document.getElementById('next-maneuver-text');
    const nextManeuverDistance = document.getElementById('next-maneuver-distance');

    // Set maneuver icon
    nextManeuverIcon.innerHTML = getManeuverIcon(nextManeuver.type);

    // Set maneuver text and distance
    nextManeuverText.textContent = nextManeuver.instruction;
    nextManeuverDistance.textContent = `in ${Math.round(currentStep.distance)} meters`;

    // Update total distance and duration
    const remainingKm = (remainingDistance / 1000).toFixed(1);
    const remainingMin = Math.ceil(remainingDistance / (totalDistance / (currentRoute.duration / 60)));

    document.getElementById('total-distance').textContent = `${remainingKm} km remaining`;
    document.getElementById('total-duration').textContent = `${remainingMin} min remaining`;

    // Check if we need to move to next step
    if (currentStep.distance < 20) { // Within 20 meters of next maneuver
        if (currentStepIndex < steps.length - 1) {
            currentStepIndex++;
            speak(steps[currentStepIndex].maneuver.instruction);
        } else {
            // Reached destination
            speak('You have reached your destination');
            navigationActive = false;
            document.getElementById('navigation-ui').classList.add('d-none');
        }
    }
}

function getManeuverIcon(type) {
    const icons = {
        'turn': 'fa-arrow-right',
        'straight': 'fa-arrow-up',
        'left': 'fa-arrow-left',
        'right': 'fa-arrow-right',
        'slight left': 'fa-arrow-turn-left',
        'slight right': 'fa-arrow-turn-right',
        'sharp left': 'fa-arrow-turn-left',
        'sharp right': 'fa-arrow-turn-right',
        'uturn': 'fa-rotate',
        'arrive': 'fa-flag-checkered'
    };

    return `<i class="fas ${icons[type] || 'fa-arrow-up'}"></i>`;
}

function calculateRemainingDistance() {
    if (!currentRoute || !userLocation) return 0;

    const steps = currentRoute.legs[0].steps;
    let remainingDistance = 0;

    // Add distance from current position to next step
    const currentStep = steps[currentStepIndex];
    const currentStepCoords = currentStep.maneuver.location;
    const distanceToNextStep = turf.distance(
        turf.point(userLocation),
        turf.point(currentStepCoords),
        { units: 'meters' }
    );
    remainingDistance += distanceToNextStep;

    // Add remaining step distances
    for (let i = currentStepIndex; i < steps.length; i++) {
        remainingDistance += steps[i].distance;
    }

    return remainingDistance;
}

function displayRoute(routeData) {
    console.log('Displaying route');
    if (!routeData.routes?.length) throw new Error('No route found');

    // Clear existing route
    if (map.getSource('route')) {
        map.removeLayer('route-glow');
        map.removeLayer('route');
        map.removeSource('route');
    }

    // Get the route coordinates
    let routeCoordinates = routeData.routes[0].geometry.coordinates;

    // Ensure the route starts and ends exactly at the markers
    if (startLocationMarker && endLocationMarker) {
        const startCoords = startLocationMarker.getLngLat().toArray();
        const endCoords = endLocationMarker.getLngLat().toArray();
        
        // Add start and end coordinates to ensure connection
        routeCoordinates = [
            startCoords,
            ...routeCoordinates,
            endCoords
        ];
    }

    // Add new route layer with improved styling
    map.addSource('route', {
        type: 'geojson',
        data: {
            type: 'Feature',
            properties: {},
            geometry: {
                type: 'LineString',
                coordinates: routeCoordinates
            }
        }
    });

    // Add the main route line
    map.addLayer({
        id: 'route',
        type: 'line',
        source: 'route',
        layout: {
            'line-join': 'round',
            'line-cap': 'round'
        },
        paint: {
            'line-color': '#3a86ff',
            'line-width': 5,
            'line-opacity': 0.8
        }
    });

    // Add a glow effect to the route
    map.addLayer({
        id: 'route-glow',
        type: 'line',
        source: 'route',
        layout: {
            'line-join': 'round',
            'line-cap': 'round'
        },
        paint: {
            'line-color': '#3a86ff',
            'line-width': 8,
            'line-opacity': 0.2,
            'line-blur': 2
        }
    });

    // Ensure markers are on top
    if (startLocationMarker) {
        startLocationMarker.getElement().style.zIndex = '1000';
    }
    if (endLocationMarker) {
        endLocationMarker.getElement().style.zIndex = '1000';
    }

    // Update route summary
    const route = routeData.routes[0];
    const distanceKm = (route.distance / 1000).toFixed(1);
    const durationMin = Math.ceil(route.duration / 60);

    // Update the route summary display
    document.getElementById('total-distance').textContent = `${distanceKm} km`;
    document.getElementById('total-duration').textContent = `${durationMin} min`;

    // Update route info display
    const routeInfoContainer = document.getElementById('route-info');
    if (routeInfoContainer) {
        routeInfoContainer.innerHTML = `
            <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-bold">Route Summary</div>
                    <div class="text-primary">
                        <i class="fas fa-route"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-road me-2"></i>
                        <span class="fw-bold">${distanceKm} km</span>
                    </div>
                    <div>
                        <i class="fas fa-clock me-2"></i>
                        <span class="fw-bold">${durationMin} min</span>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Estimated time based on ${document.getElementById('travel-mode').value} speed
                </div>
            </div>
        `;
        routeInfoContainer.classList.remove('d-none');
    }

    // Fit map to route with padding
    const bounds = new mapboxgl.LngLatBounds();
    routeCoordinates.forEach(coord => bounds.extend(coord));
    map.fitBounds(bounds, {
        padding: { top: 50, bottom: 50, left: 50, right: 50 },
        maxZoom: 17
    });

    console.log('Route displayed successfully');
}

function showInstructions(steps) {
    console.log('Displaying instructions');
    const container = document.getElementById('steps-container');
    if (!container) {
        console.warn('Steps container not found');
        return;
    }

    container.innerHTML = steps.map((step, index) => `
        <div class="d-flex mb-2">
            <div class="me-3 text-primary fw-bold">${index + 1}</div>
            <div>
                <div>${step.maneuver.instruction}</div>
                <small class="text-muted">${(step.distance / 1000).toFixed(1)} km · ${Math.ceil(step.duration / 60)} min</small>
            </div>
        </div>
    `).join('');

    const instructionsElement = document.getElementById('navigation-instructions');
    if (instructionsElement) {
        instructionsElement.classList.remove('d-none');
    }
}

function clearRoute() {
    // Remove route layers
    if (map.getSource('route')) {
        map.removeLayer('route-glow');
        map.removeLayer('route');
        map.removeSource('route');
    }

    // Remove markers
    if (startLocationMarker) {
        startLocationMarker.remove();
        startLocationMarker = null;
    }
    if (endLocationMarker) {
        endLocationMarker.remove();
        endLocationMarker = null;
    }

    // Clear input fields
    document.getElementById('start-location-input').value = '';
    document.getElementById('destination-input').value = '';

    // Hide UI elements
    const instructionsElement = document.getElementById('navigation-instructions');
    const routeInfoElement = document.getElementById('route-info');
    const navigationUI = document.getElementById('navigation-ui');

    if (instructionsElement) {
        instructionsElement.classList.add('d-none');
    }

    if (routeInfoElement) {
        routeInfoElement.classList.add('d-none');
    }

    if (navigationUI) {
        navigationUI.classList.add('d-none');
    }

    // Reset navigation state
    navigationActive = false;
    currentRoute = null;
    currentStepIndex = 0;
    routeProgress = 0;
}

function handleNavigationError(error, displayElement) {
    console.error('Navigation error:', error);
    if (displayElement) {
        displayElement.textContent = error.message || 'An unknown error occurred';
        displayElement.classList.remove('d-none');
    } else {
        alert(error.message || 'Navigation error occurred');
    }
}

// Helper functions
async function geocodeAddress(address) {
    console.log('Geocoding address:', address);
    try {
        const response = await fetch(
            `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?` +
            `access_token=${mapboxgl.accessToken}&country=ZA`
        );

        if (!response.ok) throw new Error('Geocoding failed');

        const data = await response.json();
        console.log('Geocoding response:', data);

        if (!data.features?.length) throw new Error('Location not found');

        return data.features[0].geometry.coordinates;
    } catch (error) {
        console.error('Geocoding error:', error);
        throw new Error('Failed to find the specified location. Please try a different address.');
    }
}

async function calculateRoute({ origin, destination, profile }) {
    console.log('Calculating route:', { origin, destination, profile });
    try {
        // Make sure origin and destination are arrays
        const originCoords = Array.isArray(origin) ? origin : origin.split(',').map(Number);
        const destCoords = Array.isArray(destination) ? destination : destination.split(',').map(Number);

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            throw new Error('Security token missing. Please refresh the page and try again.');
        }

        // Map the profile to the correct Mapbox profile
        const mapboxProfile = {
            'walking': 'walking',
            'driving': 'driving',
            'cycling': 'cycling'
        }[profile] || 'walking';

        console.log('Sending route request with params:', {
            origin: `${originCoords[0]},${originCoords[1]}`,
            destination: `${destCoords[0]},${destCoords[1]}`,
            profile: mapboxProfile
        });

        const response = await fetch('/technician/route', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            },
            body: JSON.stringify({
                origin: `${originCoords[0]},${originCoords[1]}`,
                destination: `${destCoords[0]},${destCoords[1]}`,
                profile: mapboxProfile
            })
        });

        const data = await response.json();

        if (!response.ok) {
            console.error('Route API error:', response.status, data);
            const errorMessage = data.error || data.message || 'Failed to calculate route';

            // Show error in UI
            const errorDisplay = document.getElementById('navigation-error');
            if (errorDisplay) {
                errorDisplay.textContent = errorMessage;
                errorDisplay.classList.remove('d-none');

                // Add suggestion for long routes
                if (errorMessage.includes('too long')) {
                    errorDisplay.innerHTML += `
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Try selecting points closer together or use a different travel mode.
                            </small>
                        </div>
                    `;
                }
            }

            throw new Error(errorMessage);
        }

        console.log('Route API response:', data);

        if (!data.routes || data.routes.length === 0) {
            throw new Error('No route found between these points');
        }

        // Validate the route based on travel mode
        const route = data.routes[0];
        const distanceKm = route.distance / 1000;

        // Add distance limits for different travel modes
        const distanceLimits = {
            'walking': 5, // 5 km for walking
            'cycling': 15, // 15 km for cycling
            'driving': 50 // 50 km for driving
        };

        if (distanceKm > distanceLimits[mapboxProfile]) {
            throw new Error(`Route is too long for ${profile} mode. Please try a different travel mode or select closer points.`);
        }

        return data;

    } catch (error) {
        console.error('Route calculation error:', error);
        const errorDisplay = document.getElementById('navigation-error');
        if (errorDisplay) {
            errorDisplay.textContent = error.message;
            errorDisplay.classList.remove('d-none');
        }
        throw error;
    }
}

// Initialize map when DOM is ready
document.addEventListener('DOMContentLoaded', initMap);

// Function to handle location selection
function handleLocationSelection(lat, lng, name, isStartLocation) {
    console.log('Handling location selection:', { lat, lng, name, isStartLocation });

    // Validate coordinates
    if (isNaN(lat) || isNaN(lng)) {
        console.error('Invalid coordinates:', { lat, lng });
        return;
    }

    // Create marker element with label
    const el = document.createElement('div');
    el.className = 'location-marker';
    el.innerHTML = `
        <div class="location-marker-content">
            <i class="fas ${isStartLocation ? 'fa-map-marker-alt' : 'fa-flag'}"></i>
            <div class="location-marker-tooltip">${name}</div>
        </div>
    `;

    // Remove existing marker
    if (isStartLocation) {
        if (startLocationMarker) {
            startLocationMarker.remove();
        }
        startLocationMarker = new mapboxgl.Marker(el)
            .setLngLat([parseFloat(lng), parseFloat(lat)])
            .addTo(map);

        // Update start location input
        document.getElementById('start-location-input').value = name;
        document.getElementById('start-location-results').classList.add('d-none');
    } else {
        if (endLocationMarker) {
            endLocationMarker.remove();
        }
        endLocationMarker = new mapboxgl.Marker(el)
            .setLngLat([parseFloat(lng), parseFloat(lat)])
            .addTo(map);

        // Update destination input
        document.getElementById('destination-input').value = name;
        document.getElementById('destination-results').classList.add('d-none');
    }

    // Center map on selected location
    map.flyTo({
        center: [parseFloat(lng), parseFloat(lat)],
        zoom: 17,
        essential: true
    });

    // Calculate route if both points are set
    if (startLocationMarker && endLocationMarker) {
        calculateRoute({
            origin: startLocationMarker.getLngLat().toArray(),
            destination: endLocationMarker.getLngLat().toArray(),
            profile: document.getElementById('travel-mode').value
        });
    }
}

// Function to handle location search
async function searchLocations(query, resultsContainer, isStartLocation) {
    console.log('Searching locations:', { query, isStartLocation });

    if (query.length < 2) {
        resultsContainer.classList.add('d-none');
        return;
    }

    try {
        const response = await fetch(`/technician/search-locations?query=${encodeURIComponent(query)}`);
        const locations = await response.json();

        resultsContainer.innerHTML = '';

        if (locations.length === 0) {
            resultsContainer.innerHTML = `
                <div class="list-group-item text-muted">
                    No locations found
                </div>
            `;
        } else {
            locations.forEach(location => {
                const item = document.createElement('div');
                item.className = 'list-group-item';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${location.building_name}</h6>
                            <small class="text-muted">
                                ${location.floor_number ? `Floor ${location.floor_number}` : ''}
                                ${location.room_number ? `• Room ${location.room_number}` : ''}
                            </small>
                        </div>
                        <button class="btn btn-sm btn-primary select-location"
                                data-lat="${location.latitude}"
                                data-lng="${location.longitude}"
                                data-name="${location.building_name}">
                            Select
                        </button>
                    </div>
                `;

                // Add click handler for the select button
                const selectButton = item.querySelector('.select-location');
                selectButton.addEventListener('click', () => {
                    const lat = parseFloat(selectButton.dataset.lat);
                    const lng = parseFloat(selectButton.dataset.lng);
                    const name = selectButton.dataset.name;
                    handleLocationSelection(lat, lng, name, isStartLocation);
                });

                resultsContainer.appendChild(item);
            });
        }

        resultsContainer.classList.remove('d-none');
    } catch (error) {
        console.error('Error searching locations:', error);
        resultsContainer.innerHTML = `
            <div class="list-group-item text-danger">
                Error searching locations
            </div>
        `;
        resultsContainer.classList.remove('d-none');
    }
}

// Function to load all locations
async function loadAllLocations() {
    console.log('Loading all locations');
    try {
        const response = await fetch('/technician/search-locations?query=');
        const locations = await response.json();

        const allLocationsList = document.getElementById('all-locations-list');
        allLocationsList.innerHTML = '';

        if (locations.length === 0) {
            allLocationsList.innerHTML = `                <div class="list-group-item text-muted">
                    No locations found
                </div>
            `;
            return;
        }

        locations.forEach(location => {
            // Validate coordinates before creating the item
            if (!location.latitude || !location.longitude) {
                console.error('Location missing coordinates:', location);
                return;
            }

            const item = document.createElement('div');
            item.className = 'list-group-item';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${location.building_name}</h6>
                        <small class="text-muted">
                            ${location.floor_number ? `Floor ${location.floor_number}` : ''}
                            ${location.room_number ? `• Room ${location.room_number}` : ''}
                        </small>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary set-start-location"
                                data-lat="${location.latitude}"
                                data-lng="${location.longitude}"
                                data-name="${location.building_name}">
                            <i class="fas fa-map-marker-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger set-end-location"
                                data-lat="${location.latitude}"
                                data-lng="${location.longitude}"
                                data-name="${location.building_name}">
                            <i class="fas fa-flag"></i>
                        </button>
                    </div>
                </div>
            `;

            // Add click handlers for the buttons
            const startButton = item.querySelector('.set-start-location');
            const endButton = item.querySelector('.set-end-location');

            startButton.addEventListener('click', () => {
                const lat = parseFloat(startButton.dataset.lat);
                const lng = parseFloat(startButton.dataset.lng);
                const name = startButton.dataset.name;

                if (isNaN(lat) || isNaN(lng)) {
                    console.error('Invalid coordinates from button:', { lat, lng });
                    return;
                }

                handleLocationSelection(lat, lng, name, true);
            });

            endButton.addEventListener('click', () => {
                const lat = parseFloat(endButton.dataset.lat);
                const lng = parseFloat(endButton.dataset.lng);
                const name = endButton.dataset.name;

                if (isNaN(lat) || isNaN(lng)) {
                    console.error('Invalid coordinates from button:', { lat, lng });
                    return;
                }

                handleLocationSelection(lat, lng, name, false);
            });

            allLocationsList.appendChild(item);
        });

    } catch (error) {
        console.error('Error loading locations:', error);
        const allLocationsList = document.getElementById('all-locations-list');
        allLocationsList.innerHTML = `
            <div class="list-group-item text-danger">
                Error loading locations
            </div>
        `;
    }
}

// Add event listeners for search inputs
document.addEventListener('DOMContentLoaded', () => {
    const startLocationInput = document.getElementById('start-location-input');
    const startLocationResults = document.getElementById('start-location-results');
    const destinationInput = document.getElementById('destination-input');
    const destinationResults = document.getElementById('destination-results');
    const useMyLocationBtn = document.getElementById('get-user-location');

    let startSearchTimeout, destSearchTimeout;

    // Add click handler for "Use My Location" button
    if (useMyLocationBtn) {
        useMyLocationBtn.addEventListener('click', () => {
            getUserLocation();
        });
    }

    startLocationInput.addEventListener('input', function(e) {
        clearTimeout(startSearchTimeout);
        startSearchTimeout = setTimeout(() => {
            searchLocations(e.target.value, startLocationResults, true);
        }, 300);
    });

    destinationInput.addEventListener('input', function(e) {
        clearTimeout(destSearchTimeout);
        destSearchTimeout = setTimeout(() => {
            searchLocations(e.target.value, destinationResults, false);
        }, 300);
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!startLocationResults.contains(e.target) && e.target !== startLocationInput) {
            startLocationResults.classList.add('d-none');
        }
        if (!destinationResults.contains(e.target) && e.target !== destinationInput) {
            destinationResults.classList.add('d-none');
        }
    });

    // Load all locations
    loadAllLocations();
});

// Add styles for location markers
const style = document.createElement('style');
style.textContent = `
    .location-marker {
        cursor: pointer;
        width: 30px;
        height: 30px;
        background-color: #4285F4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .location-marker-content {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .location-marker-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        padding: 8px 12px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        white-space: nowrap;
        display: none;
        z-index: 1;
        margin-bottom: 8px;
        font-size: 12px;
        color: #333;
    }

    .location-marker:hover .location-marker-tooltip {
        display: block;
    }

    .location-marker i {
        font-size: 16px;
    }

    .mapboxgl-popup-content {
        padding: 0;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .mapboxgl-popup-close-button {
        padding: 4px 8px;
        font-size: 16px;
        color: #666;
    }
`;
document.head.appendChild(style);

// Update the travel mode select options
document.addEventListener('DOMContentLoaded', () => {
    const travelModeSelect = document.getElementById('travel-mode');
    if (travelModeSelect) {
        travelModeSelect.innerHTML = `
            <option value="walking">Walking (up to 5 km)</option>
            <option value="cycling">Cycling (up to 15 km)</option>
            <option value="driving">Driving (up to 50 km)</option>
        `;
    }
});

// Add event listener for travel mode changes
document.addEventListener('DOMContentLoaded', () => {
    const travelModeSelect = document.getElementById('travel-mode');
    if (travelModeSelect) {
        travelModeSelect.addEventListener('change', () => {
            if (startLocationMarker && endLocationMarker) {
                calculateRoute({
                    origin: startLocationMarker.getLngLat().toArray(),
                    destination: endLocationMarker.getLngLat().toArray(),
                    profile: travelModeSelect.value
                }).then(routeData => {
                    displayRoute(routeData);
                }).catch(error => {
                    console.error('Route calculation error:', error);
                    handleNavigationError(error);
                });
            }
        });
    }
});
</script>
@endpush
@push('styles')
<style>
#map { height: 85vh; width: 100% !important; }
.navigation-controls { height: 85vh; overflow-y: auto; }
#navigation-instructions { max-height: 300px; overflow-y: auto; }
#loading-indicator { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; }
.mapboxgl-ctrl-geolocate { margin: 10px; }
.user-location-marker {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #4285F4;
    border: 2px solid white;
    box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.5);
}

#location-info {
    max-width: 250px;
    font-size: 0.85rem;
    z-index: 100;
}
.container-fluid{
    width: 100%;
}
.location-tracking-button {
    z-index: 100;
    background-color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    font-size: 14px;
    cursor: pointer;
}

.location-tracking-button:hover {
    background-color: #f0f0f0;
}

#navigation-ui {
    z-index: 1000;
    border-top: 1px solid rgba(0,0,0,0.1);
}

#next-maneuver-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(66, 133, 244, 0.1);
    border-radius: 50%;
}

.progress {
    background-color: rgba(0,0,0,0.1);
}

.progress-bar {
    background-color: #4285F4;
    transition: width 0.3s ease;
}

#location-permission-alert {
    max-width: 400px;
    width: 90%;
}
</style>
@endpush


