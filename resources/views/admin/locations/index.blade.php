@extends('Layouts.AdminNavBar')
@section('title', 'View Locations')
@section('content')
    <div class="container-fluid locations-management py-4">
        <div class="card border-0 shadow-sm">
            <!-- Card Header -->
            <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h2 class="h5 mb-1">Location Management</h2>
                        <p class="text-muted small mb-0">Manage all building locations and rooms</p>
                    </div>

                    <!-- Search Bar -->
                    <div class="search-container w-100 w-md-auto">
                        <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                            <input type="text"
                                   id="locationSearch"
                                   class="form-control"
                                   placeholder="Search buildings, floors or rooms..."
                                   aria-label="Search locations">
                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4">Building</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th class="pe-4" width="180">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="locationsTableBody">
                        @forelse($locations as $location)
                            <tr class="location-row"
                                data-building="{{ strtolower($location->building_name) }}"
                                data-floor="{{ $location->floor_number }}"
                                data-room="{{ $location->room_number ?? '' }}">
                                <!-- Building -->
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container text-primary me-2">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <span class="fw-medium building-name">{{ $location->building_name }}</span>
                                    </div>
                                </td>

                                <!-- Floor -->
                                <td>
                                <span class="badge  floor-number">
                                    Floor {{ $location->floor_number }}
                                </span>
                                </td>

                                <!-- Room -->
                                <td>
                                    @if($location->room_number)
                                        <span class="badge bg-soft-info room-number">
                                        Room {{ $location->room_number }}
                                    </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="pe-4">
                                    <div class="d-flex">
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.locations.edit', $location->location_id) }}"
                                           class="btn btn-sm btn-soft-primary me-2"
                                           title="Edit">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>

                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.locations.destroy', $location->location_id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-soft-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this location?')">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="mb-1">No Locations Found</h5>
                                        <p class="text-muted small">Add your first location to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <!-- Search Error State (Hidden by Default) -->
                    <div id="searchErrorState" class="text-center py-5 d-none">
                        <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                        <h5 class="mb-1">No Matching Locations Found</h5>
                        <p class="text-muted small">Try adjusting your search query</p>
                        <button class="btn btn-sm btn-outline-primary mt-2" id="resetSearch">
                            <i class="fas fa-undo me-1"></i> Reset Search
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination px-4 py-3">
                        {{ $locations->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Enhanced Styles */
        .search-container {
            max-width: 400px;
        }

        #searchErrorState {
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
        }
        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
            color: #212529; /* Ensure default dark text */
        }

        .bg-soft-secondary {
            background-color: rgba(108, 117, 125, 0.2);
            color: #212529 !important; /* Force dark text */
        }

        .bg-soft-info {
            background-color: rgba(23, 162, 184, 0.2);
            color: #212529 !important; /* Force dark text */
        }

        .search-highlight {
            background-color: #FFF9C4;
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
            }

            .search-container {
                width: 100%;
                margin-top: 1rem;
            }

            .table td, .table th {
                padding: 0.75rem 0.5rem;
            }

            .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('locationSearch');
            const clearSearch = document.getElementById('clearSearch');
            const resetSearch = document.getElementById('resetSearch');
            const locationRows = document.querySelectorAll('.location-row');
            const emptyState = document.querySelector('.empty-state')?.closest('tr');
            const searchErrorState = document.getElementById('searchErrorState');
            const tableBody = document.getElementById('locationsTableBody');

            function filterLocations() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                // Hide the default empty state if it exists
                if (emptyState) {
                    emptyState.style.display = 'none';
                }

                // Search through each location row
                locationRows.forEach(row => {
                    const building = row.dataset.building;
                    const floor = row.dataset.floor;
                    const room = row.dataset.room;

                    const matchesSearch = searchTerm === '' ||
                        building.includes(searchTerm) ||
                        floor.includes(searchTerm) ||
                        room.includes(searchTerm);

                    if (matchesSearch) {
                        row.style.display = '';
                        visibleCount++;

                        // Highlight matching text if search term exists
                        if (searchTerm) {
                            highlightMatches(row, searchTerm);
                        } else {
                            removeHighlights(row);
                        }
                    } else {
                        row.style.display = 'none';
                        removeHighlights(row);
                    }
                });

                // Show appropriate state
                if (searchTerm && visibleCount === 0) {
                    // Show search error state
                    searchErrorState.classList.remove('d-none');
                    if (emptyState) emptyState.style.display = 'none';
                    tableBody.style.display = 'none';
                } else {
                    // Show normal results
                    searchErrorState.classList.add('d-none');
                    tableBody.style.display = '';
                    if (visibleCount === 0 && emptyState) {
                        emptyState.style.display = '';
                    }
                }
            }

            function highlightMatches(row, searchTerm) {
                const elements = [
                    row.querySelector('.building-name'),
                    row.querySelector('.floor-number'),
                    row.querySelector('.room-number')
                ];

                elements.forEach(el => {
                    if (!el) return;

                    const text = el.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        const regex = new RegExp(searchTerm, 'gi');
                        el.innerHTML = el.textContent.replace(regex,
                            match => `<span class="search-highlight">${match}</span>`
                        );
                    }
                });
            }

            function removeHighlights(row) {
                const highlights = row.querySelectorAll('.search-highlight');
                highlights.forEach(highlight => {
                    const parent = highlight.parentNode;
                    parent.textContent = parent.textContent;
                });
            }

            // Event Listeners
            searchInput.addEventListener('input', filterLocations);

            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                filterLocations();
            });

            resetSearch?.addEventListener('click', function() {
                searchInput.value = '';
                filterLocations();
                searchInput.focus();
            });

            // Initialize with empty search
            filterLocations();
        });
    </script>
@endsection
