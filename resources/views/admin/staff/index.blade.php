@extends('Layouts.AdminNavBar')
@section('title', 'Manage Staff Members')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm">
            <!-- Header -->
            <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h2 class="h5 mb-1">Staff Management</h2>
                        <p class="text-muted small mb-0">Manage all staff member accounts and information</p>
                    </div>
                    <div class="search-container w-100 w-md-auto">
                        <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                            <input type="text"
                                   id="staffSearch"
                                   class="form-control"
                                   placeholder="Search staff by name, ID, department..."
                                   aria-label="Search staff">
                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4">Staff</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="staffTableBody">
                        @forelse($staff as $member)
                            <tr class="border-top staff-row"
                                data-name="{{ strtolower($member->first_name.' '.$member->last_name) }}"
                                data-id="{{ $member->user_id }}"
                                data-department="{{ strtolower($member->staffDetail->department ?? '') }}"
                                data-position="{{ strtolower($member->staffDetail->position_title ?? '') }}"
                                data-email="{{ strtolower($member->email) }}"
                                data-phone="{{ $member->phone_number ?? '' }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-medium staff-name">{{ $member->first_name }} {{ $member->last_name }}</div>
                                            <small class="text-muted staff-id">ID: {{ $member->user_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-primary staff-email">{{ $member->email }}</div>
                                    <small class="text-muted staff-phone">{{ $member->phone_number ?? 'No phone' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted staff-department">{{ $member->staffDetail->department ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted staff-position">{{ $member->staffDetail->position_title ?? 'N/A' }}</small>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end">
                                        <!-- View Modal Button -->
                                        <button type="button" class="btn btn-sm btn-soft-info me-2" data-bs-toggle="modal" data-bs-target="#staffModal{{ $member->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Staff Modal -->
                            <div class="modal fade" id="staffModal{{ $member->id }}" tabindex="-1" aria-labelledby="staffModalLabel{{ $member->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staffModalLabel{{ $member->id }}">Staff Member Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Full Name</dt>
                                                <dd class="col-sm-8">{{ $member->first_name }} {{ $member->last_name }}</dd>

                                                <dt class="col-sm-4">Email</dt>
                                                <dd class="col-sm-8">{{ $member->email }}</dd>

                                                <dt class="col-sm-4">Phone Number</dt>
                                                <dd class="col-sm-8">{{ $member->phone_number ?? 'N/A' }}</dd>

                                                <dt class="col-sm-4">Department</dt>
                                                <dd class="col-sm-8">{{ $member->staffDetail->department ?? 'N/A' }}</dd>

                                                <dt class="col-sm-4">Position</dt>
                                                <dd class="col-sm-8">{{ $member->staffDetail->position_title ?? 'N/A' }}</dd>

                                                <dt class="col-sm-4">Address</dt>
                                                <dd class="col-sm-8">{{ $member->address ?? 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                        <h5 class="mb-1">No Staff Found</h5>
                                        <p class="text-muted small">Add your first staff member to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <!-- Search Error State -->
                    <div id="searchErrorState" class="text-center py-5 d-none">
                        <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                        <h5 class="mb-1">No Matching Staff Found</h5>
                        <p class="text-muted small">Try adjusting your search criteria</p>
                        <button class="btn btn-sm btn-outline-primary mt-2" id="resetSearch">
                            <i class="fas fa-undo me-1"></i> Reset Search
                        </button>
                    </div>
                </div>

                @if($staff->hasPages())
                    <div class="card-footer bg-transparent border-0 pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $staff->firstItem() }} to {{ $staff->lastItem() }} of {{ $staff->total() }} entries
                            </div>
                            <div>
                                {{ $staff->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Search Container */
            .search-container {
                max-width: 400px;
            }

            /* Search Error State */
            #searchErrorState {
                background-color: #f8f9fa;
                border-top: 1px solid #eee;
            }

            /* Highlight for search matches */
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
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('staffSearch');
                const clearSearch = document.getElementById('clearSearch');
                const resetSearch = document.getElementById('resetSearch');
                const staffRows = document.querySelectorAll('.staff-row');
                const emptyState = document.querySelector('.empty-state')?.closest('tr');
                const searchErrorState = document.getElementById('searchErrorState');
                const tableBody = document.getElementById('staffTableBody');

                function filterStaff() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    let visibleCount = 0;

                    // Hide the default empty state if it exists
                    if (emptyState) {
                        emptyState.style.display = 'none';
                    }

                    // Search through each staff row
                    staffRows.forEach(row => {
                        const name = row.dataset.name;
                        const id = row.dataset.id;
                        const department = row.dataset.department;
                        const position = row.dataset.position;
                        const email = row.dataset.email;
                        const phone = row.dataset.phone;

                        const matchesSearch = searchTerm === '' ||
                            name.includes(searchTerm) ||
                            id.includes(searchTerm) ||
                            department.includes(searchTerm) ||
                            position.includes(searchTerm) ||
                            email.includes(searchTerm) ||
                            phone.includes(searchTerm);

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
                        row.querySelector('.staff-name'),
                        row.querySelector('.staff-id'),
                        row.querySelector('.staff-email'),
                        row.querySelector('.staff-phone'),
                        row.querySelector('.staff-department'),
                        row.querySelector('.staff-position')
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
                searchInput.addEventListener('input', filterStaff);

                clearSearch.addEventListener('click', function() {
                    searchInput.value = '';
                    filterStaff();
                });

                resetSearch?.addEventListener('click', function() {
                    searchInput.value = '';
                    filterStaff();
                    searchInput.focus();
                });

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Initialize with empty search
                filterStaff();
            });
        </script>
    @endpush
@endsection
