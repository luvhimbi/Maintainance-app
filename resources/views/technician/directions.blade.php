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
                            Campus Navigation
                        </h4>
                        <a href="{{ route('technician.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
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
                            </div>

                            <!-- Location Permission Alert -->
                            <div id="location-permission-alert" class="position-absolute top-50 start-50 translate-middle bg-white p-4 rounded shadow-lg d-none" style="z-index: 1000;">
                                <div class="text-center mb-3">
                                    <i class="fas fa-map-marker-alt text-primary fa-3x"></i>
                                </div>
                                <h5 class="text-center mb-3">Location Access Required</h5>
                                <p class="text-center mb-3">Please enable location access to use navigation features.</p>
                                <div class="d-grid">
                                    <button id="request-location" class="btn btn-primary">
                                        <i class="fas fa-location-arrow me-2"></i>Enable Location
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Controls Panel -->
                        <div class="col-lg-4">
                            <div class="p-3" style="height: 75vh; overflow-y: auto;">
                                <div class="navigation-controls">
                                    <div class="mb-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-directions me-2"></i>
                                            Navigation Controls
                                        </h5>

                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-flag-checkered"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control"
                                                   id="destination-input"
                                                   placeholder="Enter destination address">
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

                                    <div id="clicked-points" class="mt-4 p-3 bg-light rounded d-none">
                                        <h6><i class="fas fa-map-marker-alt me-2"></i> Clicked Points</h6>
                                        <div id="clicked-points-container" class="mt-2"></div>
                                        <button id="clear-points" class="btn btn-sm btn-outline-danger mt-2">
                                            <i class="fas fa-trash me-1"></i> Clear Points
                                        </button>
                                    </div>

                                    <div id="distances" class="mt-4 p-3 bg-light rounded d-none">
                                        <h6><i class="fas fa-ruler me-2"></i> Distances</h6>
                                        <div id="distances-container" class="mt-2"></div>
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
let clickedPoints = [];
let watchId = null;
let currentRoute = null;
let navigationActive = false;
let voiceEnabled = true;
let routeProgress = 0;
let currentStepIndex = 0;
let speechSynthesis = window.speechSynthesis;
let userLocationMarker = null;
let geolocateControl = null;

// Initialize Mapbox map
function initMap() {
    try {
        map = new mapboxgl.Map({
            container: 'map',
            style: '{{ $mapboxStyle }}',
            center: [28.098271679102634, -25.53978422415537],
            zoom: 16 // Closer zoom for campus view
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

        // Wait for map to load before adding event listeners
        map.on('load', () => {
            console.log('Map loaded successfully');

            // Check if geolocation is supported
            if (!navigator.geolocation) {
                showLocationError('Geolocation is not supported by your browser');
                return;
            }

            // Request location permission
            requestLocationPermission();

            // Setup event listeners
            setupEventListeners();

            // Add campus boundaries
            addCampusBoundaries();
        });

    } catch (error) {
        console.error('Map initialization failed:', error);
        showLocationError('Failed to initialize map. Please check your connection.');
    }
}

// Add campus boundaries
function addCampusBoundaries() {
    // TUT Soshanguve campus boundary coordinates
    const campusBoundary = {
        'type': 'Feature',
        'properties': {},
        'geometry': {
            'type': 'Polygon',
            'coordinates': [[
                [28.098271679102634, -25.53978422415537], // Main entrance
                [28.0985, -25.5399], // North boundary
                [28.0987, -25.5397], // East boundary
                [28.0983, -25.5395], // South boundary
                [28.0981, -25.5396], // West boundary
                [28.098271679102634, -25.53978422415537]  // Back to start
            ]]
        }
    };

    // Add the boundary to the map
    map.addSource('campus-boundary', {
        'type': 'geojson',
        'data': campusBoundary
    });

    // Add the boundary layer
    map.addLayer({
        'id': 'campus-boundary-fill',
        'type': 'fill',
        'source': 'campus-boundary',
        'paint': {
            'fill-color': '#0080ff',
            'fill-opacity': 0.1
        }
    });

    map.addLayer({
        'id': 'campus-boundary-line',
        'type': 'line',
        'source': 'campus-boundary',
        'paint': {
            'line-color': '#0080ff',
            'line-width': 2
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

    watchId = navigator.geolocation.watchPosition(
        updateUserLocation,
        handleGeolocationError,
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );

    // Show location info
    const locationInfo = document.getElementById('location-info');
    if (locationInfo) {
        locationInfo.classList.remove('d-none');
    }
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
    const clearPointsBtn = document.getElementById('clear-points');
    const toggleVoiceBtn = document.getElementById('toggle-voice');

    if (startBtn && clearBtn) {
        startBtn.addEventListener('click', startNavigation);
        clearBtn.addEventListener('click', clearRoute);
        console.log('Navigation buttons set up');
    } else {
        console.error('Navigation buttons not found');
    }

    if (clearPointsBtn) {
        clearPointsBtn.addEventListener('click', clearClickedPoints);
        console.log('Clear points button set up');
    } else {
        console.warn('Clear points button not found');
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

    console.log('User location updated:', userLocation);

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
    console.error('Geolocation error:', error);
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
    console.log('Updating location display');
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

function handleMapClick(e) {
    const coords = e.lngLat;
    console.log('Map clicked at:', coords);

    // Check if point is within campus bounds
    if (!isWithinCampusBounds([coords.lng, coords.lat])) {
        const errorDisplay = document.getElementById('navigation-error');
        if (errorDisplay) {
            errorDisplay.textContent = 'Selected point is outside campus boundaries. Please select a point within the campus.';
            errorDisplay.classList.remove('d-none');
        }
        return;
    }

    // Add marker to map
    const marker = new mapboxgl.Marker({
        color: clickedPoints.length === 0 ? "#FF0000" : "#00FF00" // Red for first point, green for second
    })
        .setLngLat(coords)
        .addTo(map);

    // Store marker and coordinates
    markers.push(marker);
    clickedPoints.push([coords.lng, coords.lat]);

    // Update UI with clicked points
    updateClickedPointsDisplay();

    // If we have 2 points, calculate and display distance
    if (clickedPoints.length === 2) {
        calculateDistanceBetweenPoints();

        // If navigation is active, calculate route between points
        if (navigationActive) {
            calculateRoute({
                origin: clickedPoints[0],
                destination: clickedPoints[1],
                profile: document.getElementById('travel-mode').value
            });
        }
    }
}

function calculateDistanceBetweenPoints() {
    if (clickedPoints.length !== 2) return;

    const point1 = turf.point(clickedPoints[0]);
    const point2 = turf.point(clickedPoints[1]);

    // Calculate distance in meters
    const distance = turf.distance(point1, point2, { units: 'meters' });

    // Update distance display
    const distancesContainer = document.getElementById('distances-container');
    if (distancesContainer) {
        distancesContainer.innerHTML = `
            <div class="alert alert-info">
                <div class="fw-bold">Distance between points:</div>
                <div>${distance.toFixed(2)} meters</div>
                <div>(${(distance/1000).toFixed(2)} kilometers)</div>
            </div>
        `;

        document.getElementById('distances').classList.remove('d-none');
    }

    // Draw a line between points
    const line = turf.lineString([clickedPoints[0], clickedPoints[1]]);

    if (map.getSource('distance-line')) {
        map.removeLayer('distance-line');
        map.removeSource('distance-line');
    }

    map.addSource('distance-line', {
        'type': 'geojson',
        'data': line
    });

    map.addLayer({
        'id': 'distance-line',
        'type': 'line',
        'source': 'distance-line',
        'paint': {
            'line-color': '#FF0000',
            'line-width': 2,
            'line-dasharray': [2, 2]
        }
    });
}

function updateClickedPointsDisplay() {
    const container = document.getElementById('clicked-points-container');
    if (!container) {
        console.warn('Clicked points container not found');
        return;
    }

    container.innerHTML = clickedPoints.map((point, index) => `
        <div class="d-flex mb-2">
            <div class="me-3 text-danger fw-bold">${index + 1}</div>
            <div>
                <div>Point at: ${point[1].toFixed(6)}, ${point[0].toFixed(6)}</div>
            </div>
        </div>
    `).join('');

    const clickedPointsElement = document.getElementById('clicked-points');
    if (clickedPointsElement) {
        clickedPointsElement.classList.remove('d-none');
    }
}

function clearClickedPoints() {
    // Remove all markers
    markers.forEach(marker => marker.remove());
    markers = [];
    clickedPoints = [];

    // Remove distance line
    if (map.getSource('distance-line')) {
        map.removeLayer('distance-line');
        map.removeSource('distance-line');
    }

    // Update UI
    const clickedPointsElement = document.getElementById('clicked-points');
    const distancesElement = document.getElementById('distances');

    if (clickedPointsElement) {
        clickedPointsElement.classList.add('d-none');
    }

    if (distancesElement) {
        distancesElement.classList.add('d-none');
    }
}

async function startNavigation() {
    console.log('Starting navigation');
    const destinationInput = document.getElementById('destination-input');
    const profileSelect = document.getElementById('travel-mode');
    const errorDisplay = document.getElementById('navigation-error');
    const loadingIndicator = document.getElementById('loading-indicator');

    try {
        // Reset state
        if (errorDisplay) errorDisplay.classList.add('d-none');
        if (loadingIndicator) loadingIndicator.classList.remove('d-none');

        // First check if we have user location
        if (!userLocation) {
            throw new Error("Your location is not available. Please enable location tracking first.");
        }

        let destinationCoords;

        // Check if a destination was entered or if we should use last clicked point
        if (destinationInput && destinationInput.value.trim()) {
            // Geocode destination address
            console.log('Using address:', destinationInput.value);
            destinationCoords = await geocodeAddress(destinationInput.value);
        } else if (clickedPoints.length > 0) {
            // Use the last clicked point as destination
            console.log('Using last clicked point as destination');
            destinationCoords = clickedPoints[clickedPoints.length - 1];
        } else {
            throw new Error('Please enter a destination address or click a location on the map');
        }

        console.log('Origin:', userLocation, 'Destination:', destinationCoords);

        // Clear previous route before calculating new one
        clearRoute();

        // Calculate route
        const routeData = await calculateRoute({
            origin: userLocation,
            destination: destinationCoords,
            profile: profileSelect ? profileSelect.value : 'walking'
        });

        console.log('Route data received:', routeData);

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
        map.removeLayer('route');
        map.removeSource('route');
    }

    // Add new route layer
    map.addSource('route', {
        type: 'geojson',
        data: {
            type: 'Feature',
            properties: {},
            geometry: routeData.routes[0].geometry
        }
    });

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
            'line-width': 4,
            'line-opacity': 0.7
        }
    });

    // Add markers for start and end points
    const coordinates = routeData.routes[0].geometry.coordinates;

    // Start marker (blue)
    const startMarker = new mapboxgl.Marker({ color: "#3a86ff" })
        .setLngLat(coordinates[0])
        .addTo(map);

    // End marker (green)
    const endMarker = new mapboxgl.Marker({ color: "#10b981" })
        .setLngLat(coordinates[coordinates.length - 1])
        .addTo(map);

    // Store markers to clear them later
    markers.push(startMarker, endMarker);

    // Update route info display
    updateRouteInfo(routeData.routes[0]);

    // Fit map to route
    const bounds = new mapboxgl.LngLatBounds();
    routeData.routes[0].geometry.coordinates.forEach(coord => bounds.extend(coord));
    map.fitBounds(bounds, { padding: 50 });

    console.log('Route displayed successfully');
}

function updateRouteInfo(route) {
    const routeInfoContainer = document.getElementById('route-info');
    if (!routeInfoContainer) {
        console.warn('Route info container not found');
        return;
    }

    // Calculate total distance and duration
    const distanceKm = route.distance / 1000;
    const durationMin = route.duration / 60;

    routeInfoContainer.innerHTML = `
        <div class="alert alert-info">
            <div class="fw-bold mb-2">Route Summary:</div>
            <div>Distance: ${distanceKm.toFixed(2)} km</div>
            <div>Duration: ${Math.floor(durationMin)} min</div>
        </div>
    `;

    routeInfoContainer.classList.remove('d-none');
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
    console.log('Clearing route');
    // Remove route layer
    if (map.getSource('route')) {
        map.removeLayer('route');
        map.removeSource('route');
    }

    // Remove route markers (but keep clicked point markers)
    const clickedPointsCount = clickedPoints.length;
    if (markers.length > clickedPointsCount) {
        markers.slice(clickedPointsCount).forEach(marker => marker.remove());
        markers = markers.slice(0, clickedPointsCount);
    }

    // Hide UI elements
    const instructionsElement = document.getElementById('navigation-instructions');
    const routeInfoElement = document.getElementById('route-info');

    if (instructionsElement) {
        instructionsElement.classList.add('d-none');
    }

    if (routeInfoElement) {
        routeInfoElement.classList.add('d-none');
    }
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

        console.log('Sending route request with params:', {
            origin: `${originCoords[0]},${originCoords[1]}`,
            destination: `${destCoords[0]},${destCoords[1]}`,
            profile: profile
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
                profile: profile
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
                                Try selecting points closer together or use the campus navigation feature.
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

// Add a function to check if points are within campus bounds
function isWithinCampusBounds(coords) {
    const campusBounds = {
        north: -25.5395,
        south: -25.5399,
        east: 28.0987,
        west: 28.0981
    };

    return coords[1] >= campusBounds.south &&
           coords[1] <= campusBounds.north &&
           coords[0] >= campusBounds.west &&
           coords[0] <= campusBounds.east;
}

// Initialize map when DOM is ready
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush

@push('styles')
<style>
#map { height: 75vh; width: 100%; }
.navigation-controls { height: 75vh; overflow-y: auto; }
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
