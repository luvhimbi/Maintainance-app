@extends('Layouts.AdminNavBar')
@section('title', 'Manage Technicians')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4"> {{-- Added rounded-4 for consistent styling --}}
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4"> {{-- Added px-4 and rounded-top-4 --}}
                <div class="row align-items-center g-3"> {{-- Using Bootstrap row for better column control --}}
                    <div class="col-12 col-md-6"> {{-- Column for title --}}
                        <h2 class="h5 mb-1 fw-bold text-dark">Manage Maintenance Staff</h2>
                        <p class="text-muted small mb-0">Manage all technician accounts and information</p>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 offset-lg-2 d-flex flex-column flex-sm-row align-items-center justify-content-end gap-3"> {{-- Added flexbox for alignment --}}
                        <div class="input-group rounded-pill overflow-hidden shadow-sm-sm flex-grow-1"> {{-- Added flex-grow-1 --}}
                            <span class="input-group-text bg-light border-0 ps-3">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"
                                   id="technicianSearch"
                                   class="form-control border-0 pe-3"
                                   placeholder="Search technicians by name, ID, email, specialization, address, phone..." {{-- Expanded placeholder --}}
                                   aria-label="Search technicians">
                            <button class="btn btn-outline-secondary border-0"
                                    type="button"
                                    id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <a href="{{ route('admin.technicians.create') }}" class="btn btn-primary rounded-pill px-4 py-2 mt-2 mt-sm-0" id="addNewTechnicianButton"> {{-- New button, always visible --}}
                            <i class="fas fa-plus me-1"></i> Add New Technician
                        </a>
                    </div>
                </div>
                <div id="searchFeedback" class="mt-2 small text-muted text-end" style="display: none;"> {{-- Moved feedback below input group --}}
                    <span id="resultCount">0</span> technicians found
                </div>
                {{-- Removed searchError div --}}
            </div>

            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0 rounded-3 shadow-sm" role="alert"> {{-- Added rounded-3 and shadow-sm --}}
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div id="techniciansTableContainer" class="{{ $technicians->isEmpty() ? 'd-none' : '' }}"> {{-- Container for table, hidden if no technicians initially --}}
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover"> {{-- Added table-hover for better UX --}}
                            <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th class="py-3">First Name</th>
                                <th class="py-3">Last Name</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Specialization</th>
                                <th class="py-3">Address</th>
                                <th class="py-3">Phone Number</th>
                                <th class="pe-4 text-end py-3">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="techniciansTableBody">
                            @foreach($technicians as $tech)
                                <tr class="border-top technician-row"
                                    data-id="{{ $tech->user_id }}"
                                    data-firstname="{{ strtolower($tech->first_name) }}"
                                    data-lastname="{{ strtolower($tech->last_name) }}"
                                    data-email="{{ strtolower($tech->email) }}"
                                    data-specialization="{{ strtolower($tech->maintenanceStaff->specialization ?? '') }}"
                                    data-address="{{ strtolower($tech->address) }}"
                                    data-phone="{{ $tech->phone_number }}">
                                    <td class="ps-4 technician-id text-dark fw-medium">{{ $tech->user_id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="fw-bold">{{ substr($tech->first_name, 0, 1) }}{{ substr($tech->last_name, 0, 1) }}</span>
                                            </div>
                                            <div class="fw-medium technician-firstname text-dark">{{ $tech->first_name }}</div>
                                        </div>
                                    </td>
                                    <td class="technician-lastname text-dark fw-medium">{{ $tech->last_name }}</td>
                                    <td class="technician-email text-primary fw-medium">{{ $tech->email }}</td>
                                    <td class="technician-specialization text-muted">
                                        @if($tech->maintenanceStaff)
                                            {{ $tech->maintenanceStaff->specialization }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="technician-address text-muted">{{ $tech->address }}</td>
                                    <td class="technician-phone text-muted">{{ $tech->phone_number }}</td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.technicians.edit', $tech->user_id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2" {{-- Styled button --}}
                                               data-bs-toggle="tooltip"
                                               title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.technicians.destroy', $tech->user_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 delete-technician" {{-- Styled button --}}
                                                        data-name="{{ $tech->first_name }} {{ $tech->last_name }}"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- New Empty State for no technicians or no results --}}
                <div id="noTechniciansFoundState" class="text-center py-5 px-3 {{ $technicians->isNotEmpty() ? 'd-none' : '' }}"> {{-- Initially hidden if technicians exist --}}
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-user-cog fa-4x text-muted"></i> {{-- Updated icon --}}
                    </div>
                    <h4 class="fw-bold mb-2 text-dark" id="emptyStateHeading">No Technicians Found</h4>
                    <p class="text-muted mb-4" id="emptyStateText">Add your first technician to get started.</p>
                    {{-- The "Add New Technician" button is now always in the header --}}
                    <button class="btn btn-outline-secondary rounded-pill px-4 py-2 mt-2 d-none" id="resetEmptyStateFilters">
                        <i class="fas fa-sync-alt me-1"></i> Reset Filters
                    </button>
                </div>

                @if($technicians->hasPages())
                    <div class="card-footer bg-transparent border-0 pt-3 px-4">
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

    @foreach($technicians as $member)
        <div class="modal fade" id="staffModal{{ $member->user_id }}" tabindex="-1" aria-labelledby="staffModalLabel{{ $member->user_id }}" aria-hidden="true"> {{-- Changed to user_id --}}
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 shadow-lg"> {{-- Added rounded-4 and shadow-lg --}}
                    <div class="modal-header bg-light border-bottom rounded-top-4"> {{-- Styled modal header --}}
                        <h5 class="modal-title fw-bold text-dark" id="staffModalLabel{{ $member->user_id }}">Staff Member Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4"> {{-- Added padding --}}
                        <dl class="row mb-0"> {{-- Added mb-0 for last dl --}}
                            <dt class="col-sm-4 text-muted fw-medium">Full Name</dt>
                            <dd class="col-sm-8 text-dark">{{ $member->first_name }} {{ $member->last_name }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Email</dt>
                            <dd class="col-sm-8 text-dark">{{ $member->email }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Phone Number</dt>
                            <dd class="col-sm-8 text-dark">{{ $member->phone_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Department</dt>
                            <dd class="col-sm-8 text-dark">{{ $member->maintenanceStaff->department ?? 'N/A' }}</dd> {{-- Changed to maintenanceStaff --}}

                            <dt class="col-sm-4 text-muted fw-medium">Position</dt>
                            <dd class="col-sm-8 text-dark">{{ $member->maintenanceStaff->position_title ?? 'N/A' }}</dd> {{-- Changed to maintenanceStaff --}}

                            <dt class="col-sm-4 text-muted fw-medium">Address</dt>
                            <dd class="col-sm-8 text-dark">{{ $member->address ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                    <div class="modal-footer bg-light border-top rounded-bottom-4"> {{-- Styled modal footer --}}
                        <button type="button" class="btn btn-secondary rounded-pill px-4 py-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @push('styles')
        <style>
            /* General body and card styling */
            body {
                background-color: #f8f9fa; /* Light grey background */
                font-family: 'Inter', sans-serif; /* Consistent font */
                min-height: 100vh; /* Ensure body takes full viewport height */
                display: flex; /* Enable flexbox */
                flex-direction: column; /* Arrange content in a column */
            }

            .card {
                border: 1px solid #e0e0e0; /* Subtle border for cards */
                box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05); /* Lighter shadow */
            }

            .card-header {
                background-color: #ffffff; /* White background for headers */
                color: #343a40; /* Dark text */
                border-bottom: 1px solid #e9ecef; /* Light border at the bottom */
            }

            .card-header h2, .card-header p {
                color: #343a40 !important; /* Ensure text is dark */
            }

            /* Custom subtle badge colors */
            .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
            .text-primary { color: #0d6efd !important; }

            .bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }
            .text-success { color: #28a745 !important; }

            .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
            .text-warning { color: #ffc107 !important; }

            .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
            .text-danger { color: #dc3545 !important; }

            .bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }
            .text-info { color: #17a2b8 !important; }

            .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
            .text-secondary { color: #6c757d !important; }

            /* Buttons */
            .btn-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
            .btn-primary:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
            }

            .btn-outline-secondary {
                color: #6c757d;
                border-color: #6c757d;
                background-color: #ffffff;
                transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
            }
            .btn-outline-secondary:hover {
                background-color: #6c757d;
                color: white;
                border-color: #6c757d;
            }

            /* Search input group styling */
            .input-group.rounded-pill {
                border: 1px solid #ced4da;
                border-radius: 2rem; /* More rounded */
                overflow: hidden;
            }
            .input-group.rounded-pill .form-control,
            .input-group.rounded-pill .input-group-text,
            .input-group.rounded-pill .btn {
                border: none !important; /* Remove individual borders */
                background-color: white;
            }
            .input-group.rounded-pill .form-control:focus {
                box-shadow: none; /* No focus shadow inside input group */
            }
            .input-group.rounded-pill .input-group-text {
                padding-left: 1rem;
            }
            .input-group.rounded-pill .form-control {
                padding-left: 0.5rem;
            }
            .input-group.rounded-pill .btn {
                padding-right: 1rem;
            }

            /* Table specific styles */
            .table thead th {
                font-weight: 600;
                color: #495057;
                border-bottom: 2px solid #e9ecef;
            }
            .table tbody tr {
                transition: background-color 0.2s ease;
            }
            .table tbody tr:hover {
                background-color: #f0f2f5;
            }

            /* Search highlight */
            .search-highlight {
                background-color: #FFF9C4; /* Light yellow */
                padding: 0 2px;
                border-radius: 3px;
            }

            /* Empty state styling */
            .empty-state-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(108, 117, 125, 0.1); /* Secondary subtle for empty */
                border-radius: 50%;
            }
            .empty-state-icon i {
                color: #6c757d; /* Muted color for icon */
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Technician search functionality
                const searchInput = document.getElementById('technicianSearch');
                const clearButton = document.getElementById('clearSearch');
                const techniciansTableBody = document.getElementById('techniciansTableBody');
                const techniciansTableContainer = document.getElementById('techniciansTableContainer');
                const noTechniciansFoundState = document.getElementById('noTechniciansFoundState');
                const emptyStateHeading = document.getElementById('emptyStateHeading');
                const emptyStateText = document.getElementById('emptyStateText');
                const addTechnicianButton = document.getElementById('addTechnicianButton'); // This button is now always visible in the header
                const resetEmptyStateFiltersButton = document.getElementById('resetEmptyStateFilters');
                const searchFeedback = document.getElementById('searchFeedback');
                const resultCount = document.getElementById('resultCount');
                // Removed searchError variable as the div is removed from HTML

                // Function to highlight text matches
                function highlightText(element, searchTerm) {
                    if (!element || !searchTerm) return;

                    const text = element.textContent;
                    const lowerText = text.toLowerCase();
                    let startIndex = 0;

                    // If the search term isn't found in this element, return
                    if (!lowerText.includes(searchTerm.toLowerCase())) return;

                    // Clear the element's content while preserving its original text for re-highlighting
                    const originalContent = element.innerHTML;
                    element.innerHTML = ''; // Clear current HTML

                    // Find all occurrences and highlight them
                    while (startIndex < text.length) {
                        const matchIndex = lowerText.indexOf(searchTerm.toLowerCase(), startIndex);

                        if (matchIndex === -1) {
                            // No more matches, add the remaining text
                            element.appendChild(document.createTextNode(text.substring(startIndex)));
                            break;
                        }

                        // Add text before the match
                        if (matchIndex > startIndex) {
                            element.appendChild(document.createTextNode(text.substring(startIndex, matchIndex)));
                        }

                        // Add the highlighted match
                        const highlightSpan = document.createElement('span');
                        highlightSpan.className = 'search-highlight';
                        highlightSpan.textContent = text.substring(matchIndex, matchIndex + searchTerm.length);
                        element.appendChild(highlightSpan);

                        // Move past this match
                        startIndex = matchIndex + searchTerm.length;
                    }
                }

                // Function to remove all highlights from a row
                function removeHighlightsFromRow(row) {
                    row.querySelectorAll('.search-highlight').forEach(span => {
                        const parent = span.parentNode;
                        parent.replaceChild(document.createTextNode(span.textContent), span);
                        parent.normalize(); // Merge adjacent text nodes
                    });
                }

                // Function to filter table rows based on search input
                function filterTechnicians() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    let visibleCount = 0;

                    // Get all actual technician rows
                    const technicianRows = Array.from(techniciansTableBody.children).filter(row => !row.classList.contains('empty-state-row'));

                    technicianRows.forEach(row => {
                        // Remove any existing highlights first
                        removeHighlightsFromRow(row);

                        const idEl = row.querySelector('.technician-id');
                        const firstnameEl = row.querySelector('.technician-firstname');
                        const lastnameEl = row.querySelector('.technician-lastname');
                        const emailEl = row.querySelector('.technician-email');
                        const specializationEl = row.querySelector('.technician-specialization');
                        const addressEl = row.querySelector('.technician-address');
                        const phoneEl = row.querySelector('.technician-phone');

                        const id = idEl?.textContent.toLowerCase() || '';
                        const firstname = firstnameEl?.textContent.toLowerCase() || '';
                        const lastname = lastnameEl?.textContent.toLowerCase() || '';
                        const email = emailEl?.textContent.toLowerCase() || '';
                        const specialization = specializationEl?.textContent.toLowerCase() || '';
                        const address = addressEl?.textContent.toLowerCase() || '';
                        const phone = phoneEl?.textContent.toLowerCase() || '';

                        const matchesSearch =
                            id.includes(searchTerm) ||
                            firstname.includes(searchTerm) ||
                            lastname.includes(searchTerm) ||
                            email.includes(searchTerm) ||
                            specialization.includes(searchTerm) ||
                            address.includes(searchTerm) ||
                            phone.includes(searchTerm);

                        if (matchesSearch) {
                            row.style.display = '';
                            if (searchTerm) { // Only highlight if there's a search term
                                highlightText(idEl, searchTerm);
                                highlightText(firstnameEl, searchTerm);
                                highlightText(lastnameEl, searchTerm);
                                highlightText(emailEl, searchTerm);
                                highlightText(specializationEl, searchTerm);
                                highlightText(addressEl, searchTerm);
                                highlightText(phoneEl, searchTerm);
                            }
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Update UI based on visible count
                    if (visibleCount === 0) {
                        techniciansTableContainer.classList.add('d-none');
                        noTechniciansFoundState.classList.remove('d-none');

                        // Adjust empty state message based on whether a search term is present
                        if (searchTerm) {
                            emptyStateHeading.textContent = 'No Matching Technicians Found';
                            emptyStateText.textContent = 'We couldn\'t find any technicians matching your search criteria.';
                            // addTechnicianButton.classList.add('d-none'); // This button is always visible in the header now
                            resetEmptyStateFiltersButton.classList.remove('d-none'); // Show reset filters button
                            searchFeedback.style.display = 'none';
                            // Removed searchError.style.display = 'block';
                        } else {
                            emptyStateHeading.textContent = 'No Technicians Found';
                            emptyStateText.textContent = 'Add your first technician to get started.';
                            // addTechnicianButton.classList.remove('d-none'); // This button is always visible in the header now
                            resetEmptyStateFiltersButton.classList.add('d-none'); // Hide reset filters button
                            searchFeedback.style.display = 'none';
                            // Removed searchError.style.display = 'none';
                        }
                    } else {
                        techniciansTableContainer.classList.remove('d-none');
                        noTechniciansFoundState.classList.add('d-none');
                        searchFeedback.style.display = 'block';
                        // Removed searchError.style.display = 'none';
                        resultCount.textContent = visibleCount;
                    }
                }

                // Delete confirmation with SweetAlert
                document.querySelectorAll('.delete-technician').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const form = this.closest('form');
                        const technicianName = this.getAttribute('data-name');

                        Swal.fire({
                            title: 'Delete Technician?',
                            html: `Are you sure you want to delete <strong>${technicianName}</strong>? This action cannot be undone.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545', // Red for delete
                            cancelButtonColor: '#6c757d', // Grey for cancel
                            confirmButtonText: 'Yes, delete it!',
                            customClass: {
                                confirmButton: 'btn btn-danger px-4 py-2 rounded-pill mx-2',
                                cancelButton: 'btn btn-secondary px-4 py-2 rounded-pill mx-2'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Event Listeners
                searchInput.addEventListener('input', filterTechnicians);

                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    filterTechnicians();
                });

                resetEmptyStateFiltersButton?.addEventListener('click', function() {
                    searchInput.value = '';
                    filterTechnicians();
                    searchInput.focus();
                });

                // Initial call to filterTechnicians to set the correct state on page load
                filterTechnicians();
            });
        </script>
    @endpush
@endsection
