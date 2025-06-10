@extends('Layouts.AdminNavbar')
@section('title', 'Manage Students')
@section('content')
    <div class="container-fluid student-management py-4">
        <div class="card border-0 shadow-sm rounded-4"> {{-- Added rounded-4 for consistent styling --}}
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4"> {{-- Added px-4 and rounded-top-4 --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h2 class="h5 mb-1 fw-bold text-dark">Student Management</h2>
                        <p class="text-muted small mb-0">Manage all student accounts and information</p>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                        <form method="GET" action="{{ route('admin.students.index') }}" class="w-100 d-flex">
                            <div class="input-group rounded-pill overflow-hidden shadow-sm-sm w-100">
                                <span class="input-group-text bg-light border-0 ps-3">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="studentSearch" name="search" class="form-control border-0 pe-3"
                                       placeholder="Search students..." value="{{ request('search', '') }}">
                                <button class="btn btn-outline-secondary border-0" type="submit" aria-label="Search">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary border-0" type="button" aria-label="Clear search">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="searchFeedback" class="mt-2 small text-muted text-end" style="display: none;"> {{-- Moved feedback below input group --}}
                    <span id="resultCount">0</span> students found
                </div>
                <div id="searchError" class="mt-2 small text-danger text-end" style="display: none;"> {{-- Moved error below input group --}}
                    <i class="fas fa-exclamation-circle me-1"></i> No students match your search criteria
                </div>
            </div>

            <div class="card-body p-0">
                <div id="studentsTableContainer" class="{{ $students->isEmpty() ? 'd-none' : '' }}"> {{-- Container for table, hidden if no students initially --}}
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover"> {{-- Added table-hover for better UX --}}
                            <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Student</th>
                                <th class="py-3">Contact</th>
                                <th class="py-3">Student Number</th>
                                <th class="py-3">Course</th>
                                <th class="py-3">Faculty</th>
                                <th class="py-3">Address</th>
                                <th class="pe-4 text-end py-3">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                            @foreach($students as $student)
                                <tr class="border-top">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <span class="fw-bold">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <small class="text-muted">ID: {{ $student->user_id }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="text-primary fw-medium">{{ $student->email }}</div>
                                        <small class="text-muted">{{ $student->phone_number ?? 'No phone' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->studentDetail->student_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->studentDetail->course ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->studentDetail->faculty ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->address ?? 'No Address' }}</small>
                                    </td>

                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2"> {{-- Added gap for buttons --}}
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#studentModal{{ $student->user_id }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            {{-- Add other actions here if needed, e.g., edit, delete --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- New Empty State for no students or no results --}}
                <div id="noStudentsFoundState" class="text-center py-5 px-3 {{ $students->isNotEmpty() ? 'd-none' : '' }}"> {{-- Initially hidden if students exist --}}
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-user-graduate fa-4x text-muted"></i>
                    </div>
                    <h4 class="fw-bold mb-2 text-dark" id="emptyStateHeading">No Students Found</h4>
                    <p class="text-muted mb-4" id="emptyStateText">Add your first student to get started.</p>

                    <button class="btn btn-outline-secondary rounded-pill px-4 py-2 mt-2 d-none" id="resetEmptyStateFilters">
                        <i class="fas fa-sync-alt me-1"></i> Reset Filters
                    </button>
                </div>

                @if($students->hasPages())
                    <div class="card-footer bg-transparent border-0 pt-3 px-4"> {{-- Added px-4 --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} entries
                            </div>
                            <div>
                                {{ $students->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @foreach($students as $student)
        <div class="modal fade" id="studentModal{{ $student->user_id }}" tabindex="-1" aria-labelledby="studentModalLabel{{ $student->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 shadow-lg"> {{-- Added rounded-4 and shadow-lg --}}
                    <div class="modal-header bg-light border-bottom rounded-top-4"> {{-- Styled modal header --}}
                        <h5 class="modal-title fw-bold text-dark" id="studentModalLabel{{ $student->user_id }}">Student Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4"> {{-- Added padding --}}
                        <dl class="row mb-0"> {{-- Added mb-0 for last dl --}}
                            <dt class="col-sm-4 text-muted fw-medium">Full Name</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->first_name }} {{ $student->last_name }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Email</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->email }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Phone Number</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->phone_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Student Number</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->studentDetail->student_number ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Course</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->studentDetail->course ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Faculty</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->studentDetail->faculty ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-muted fw-medium">Address</dt>
                            <dd class="col-sm-8 text-dark">{{ $student->address ?? 'N/A' }}</dd>
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Student search functionality
                const searchInput = document.getElementById('studentSearch');
                const clearButton = document.getElementById('clearSearch');
                const studentsTableBody = document.getElementById('studentsTableBody');
                const studentsTableContainer = document.getElementById('studentsTableContainer');
                const noStudentsFoundState = document.getElementById('noStudentsFoundState');
                const emptyStateHeading = document.getElementById('emptyStateHeading');
                const emptyStateText = document.getElementById('emptyStateText');
                const addStudentButton = document.getElementById('addStudentButton');
                const resetEmptyStateFiltersButton = document.getElementById('resetEmptyStateFilters');
                const searchFeedback = document.getElementById('searchFeedback');
                const resultCount = document.getElementById('resultCount');
                const searchError = document.getElementById('searchError');

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
                function filterStudents() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    let visibleCount = 0;

                    // Get all actual student rows (excluding any potential empty state rows)
                    const studentRows = Array.from(studentsTableBody.children).filter(row => !row.classList.contains('empty-state-row'));


                    studentRows.forEach(row => {
                        // Remove any existing highlights first
                        removeHighlightsFromRow(row);

                        const studentNameEl = row.querySelector('.fw-medium');
                        const studentIdEl = row.querySelector('small.text-muted');
                        const emailEl = row.querySelector('.text-primary');
                        const phoneEl = row.querySelectorAll('small.text-muted')[0]; // Assuming first small.text-muted is phone
                        const addressEl = row.querySelectorAll('small.text-muted')[1]; // Assuming second small.text-muted is address

                        const studentName = studentNameEl?.textContent.toLowerCase() || '';
                        const studentId = studentIdEl?.textContent.toLowerCase() || '';
                        const email = emailEl?.textContent.toLowerCase() || '';
                        const phone = phoneEl?.textContent.toLowerCase() || '';
                        const address = addressEl?.textContent.toLowerCase() || '';

                        const matchesSearch =
                            studentName.includes(searchTerm) ||
                            studentId.includes(searchTerm) ||
                            email.includes(searchTerm) ||
                            phone.includes(searchTerm) ||
                            address.includes(searchTerm);

                        if (matchesSearch) {
                            row.style.display = '';
                            if (searchTerm) { // Only highlight if there's a search term
                                highlightText(studentNameEl, searchTerm);
                                highlightText(studentIdEl, searchTerm);
                                highlightText(emailEl, searchTerm);
                                highlightText(phoneEl, searchTerm);
                                highlightText(addressEl, searchTerm);
                            }
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Update UI based on visible count
                    if (visibleCount === 0) {
                        studentsTableContainer.classList.add('d-none');
                        noStudentsFoundState.classList.remove('d-none');

                        // Adjust empty state message based on whether a search term is present
                        if (searchTerm) {
                            emptyStateHeading.textContent = 'No Matching Students Found';
                            emptyStateText.textContent = 'We couldn\'t find any students matching your search criteria.';
                            addStudentButton.classList.add('d-none'); // Hide add student button
                            resetEmptyStateFiltersButton.classList.remove('d-none'); // Show reset filters button
                            searchFeedback.style.display = 'none';
                            searchError.style.display = 'block';
                        } else {
                            emptyStateHeading.textContent = 'No Students Found';
                            emptyStateText.textContent = 'Add your first student to get started.';
                            addStudentButton.classList.remove('d-none'); // Show add student button
                            resetEmptyStateFiltersButton.classList.add('d-none'); // Hide reset filters button
                            searchFeedback.style.display = 'none';
                            searchError.style.display = 'none';
                        }
                    } else {
                        studentsTableContainer.classList.remove('d-none');
                        noStudentsFoundState.classList.add('d-none');
                        searchFeedback.style.display = 'block';
                        searchError.style.display = 'none';
                        resultCount.textContent = visibleCount;
                    }
                }

                // Add event listeners
                if (searchInput) {
                    searchInput.addEventListener('input', filterStudents);

                    // Clear search when X button is clicked
                    if (clearButton) {
                        clearButton.addEventListener('click', function() {
                            searchInput.value = '';
                            filterStudents(); // Re-run filter to reset state
                        });
                    }

                    // Reset filters from the empty state button
                    if (resetEmptyStateFiltersButton) {
                        resetEmptyStateFiltersButton.addEventListener('click', function() {
                            searchInput.value = '';
                            filterStudents(); // Re-run filter to reset state
                        });
                    }

                    // Add keyboard shortcut (Ctrl+F or Cmd+F) to focus search
                    document.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                            e.preventDefault();
                            searchInput.focus();
                        }
                    });
                }

                // Initial call to filterStudents to set the correct state on page load
                filterStudents();
            });
        </script>
    @endpush
@endsection
