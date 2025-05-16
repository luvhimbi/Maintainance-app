@extends('layouts.AdminNavBar')

@section('title', 'Manage Technicians')

@section('content')
    <div class="container-fluid technician-management py-4">
        <div class="card border-0 shadow-sm">
            <!-- Card Header -->
            <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h2 class="h5 mb-1">Manage Maintenance Staff</h2>
                        <p class="text-muted small mb-0">Manage all technician accounts and information</p>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <!-- Search Bar -->
                        <div class="search-container w-100 w-md-auto">
                            <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                                <input type="text"
                                       id="technicianSearch"
                                       class="form-control"
                                       placeholder="Search technicians..."
                                       aria-label="Search technicians">
                                <button class="btn btn-outline-secondary"
                                        type="button"
                                        id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add New Button -->
                        <a href="{{ route('admin.technicians.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add New
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="techniciansTableBody">
                        @forelse($technicians as $tech)
                            <tr class="border-top technician-row"
                                data-id="{{ $tech->user_id }}"
                                data-firstname="{{ strtolower($tech->first_name) }}"
                                data-lastname="{{ strtolower($tech->last_name) }}"
                                data-email="{{ strtolower($tech->email) }}"
                                data-specialization="{{ strtolower($tech->maintenanceStaff->specialization ?? '') }}"
                                data-address="{{ strtolower($tech->address) }}"
                                data-phone="{{ $tech->phone_number }}">
                                <td class="ps-4 technician-id">{{ $tech->user_id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-medium technician-firstname">{{ $tech->first_name }}</div>
                                    </div>
                                </td>
                                <td class="technician-lastname">{{ $tech->last_name }}</td>
                                <td class="technician-email">{{ $tech->email }}</td>
                                <td class="technician-specialization">
                                    @if($tech->maintenanceStaff)
                                        {{ $tech->maintenanceStaff->specialization }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="technician-address">{{ $tech->address }}</td>
                                <td class="technician-phone">{{ $tech->phone_number }}</td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.technicians.edit', $tech->user_id) }}"
                                           class="btn btn-sm btn-soft-primary"
                                           data-bs-toggle="tooltip"
                                           title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.technicians.destroy', $tech->user_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-soft-danger delete-technician"
                                                    data-name="{{ $tech->first_name }} {{ $tech->last_name }}"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-user-cog fa-3x text-muted mb-3"></i>
                                        <h5 class="mb-1">No Technicians Found</h5>
                                        <p class="text-muted small">Add your first technician to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <!-- Search Error State -->
                    <div id="searchErrorState" class="text-center py-5 d-none">
                        <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
                        <h5 class="mb-1">No Matching Technicians Found</h5>
                        <p class="text-muted small">Try adjusting your search criteria</p>
                        <button class="btn btn-sm btn-outline-primary mt-2" id="resetSearch">
                            <i class="fas fa-undo me-1"></i> Reset Search
                        </button>
                    </div>
                </div>

                @if($technicians->hasPages())
                    <div class="card-footer bg-transparent border-0 pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $technicians->firstItem() }} to {{ $technicians->lastItem() }} of {{ $technicians->total() }} entries
                            </div>
                            <div>
                                {{ $technicians->links('pagination::bootstrap-5') }}
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

            /* Button Styles */
            .btn-soft-primary {
                color: #0d6efd;
                background-color: rgba(13, 110, 253, 0.1);
                border: none;
            }

            .btn-soft-danger {
                color: #dc3545;
                background-color: rgba(220, 53, 69, 0.1);
                border: none;
            }

            .btn-soft-primary:hover {
                background-color: rgba(13, 110, 253, 0.2);
            }

            .btn-soft-danger:hover {
                background-color: rgba(220, 53, 69, 0.2);
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('technicianSearch');
                const clearSearch = document.getElementById('clearSearch');
                const resetSearch = document.getElementById('resetSearch');
                const technicianRows = document.querySelectorAll('.technician-row');
                const emptyState = document.querySelector('.empty-state')?.closest('tr');
                const searchErrorState = document.getElementById('searchErrorState');
                const tableBody = document.getElementById('techniciansTableBody');

                function filterTechnicians() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    let visibleCount = 0;

                    // Hide the default empty state if it exists
                    if (emptyState) {
                        emptyState.style.display = 'none';
                    }

                    // Search through each technician row
                    technicianRows.forEach(row => {
                        const id = row.dataset.id;
                        const firstname = row.dataset.firstname;
                        const lastname = row.dataset.lastname;
                        const email = row.dataset.email;
                        const specialization = row.dataset.specialization;
                        const address = row.dataset.address;
                        const phone = row.dataset.phone;

                        const matchesSearch = searchTerm === '' ||
                            id.includes(searchTerm) ||
                            firstname.includes(searchTerm) ||
                            lastname.includes(searchTerm) ||
                            email.includes(searchTerm) ||
                            specialization.includes(searchTerm) ||
                            address.includes(searchTerm) ||
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
                        row.querySelector('.technician-id'),
                        row.querySelector('.technician-firstname'),
                        row.querySelector('.technician-lastname'),
                        row.querySelector('.technician-email'),
                        row.querySelector('.technician-specialization'),
                        row.querySelector('.technician-address'),
                        row.querySelector('.technician-phone')
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

                // Delete confirmation with SweetAlert
                document.querySelectorAll('.delete-technician').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = this.closest('form');
                        const technicianName = this.getAttribute('data-name');

                        Swal.fire({
                            title: 'Delete Technician?',
                            html: `Are you sure you want to delete <strong>${technicianName}</strong>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Event Listeners
                searchInput.addEventListener('input', filterTechnicians);

                clearSearch.addEventListener('click', function() {
                    searchInput.value = '';
                    filterTechnicians();
                });

                resetSearch?.addEventListener('click', function() {
                    searchInput.value = '';
                    filterTechnicians();
                    searchInput.focus();
                });

                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Initialize with empty search
                filterTechnicians();
            });
        </script>
    @endpush
@endsection
