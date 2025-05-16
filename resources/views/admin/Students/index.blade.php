@extends('Layouts.AdminNavBar')
@section('title', 'Manage Students')
@section('content')
<div class="container-fluid student-management py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h2 class="h5 mb-1">Student Management</h2>
                    <p class="text-muted small mb-0">Manage all student accounts and information</p>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="studentSearch" class="form-control" placeholder="Search students...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="searchFeedback" class="mt-2 small text-muted" style="display: none;">
                        <span id="resultCount">0</span> students found
                    </div>
                    <div id="searchError" class="mt-2 small text-danger" style="display: none;">
                        <i class="fas fa-exclamation-circle me-1"></i> No students match your search criteria
                    </div>
                </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="border-top">
                            <!-- Student Info -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center">

                                    <div>
                                        <div class="fw-medium">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <small class="text-muted">ID: {{ $student->user_id }}</small>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info -->
                            <td>
                                <div class="text-primary">{{ $student->email }}</div>
                                <small class="text-muted">{{ $student->phone_number ?? 'No phone' }}</small>
                            </td>
                             <td>

                                <small class="text-muted">{{ $student->address ?? 'No Address' }}</small>
                            </td>

                            <!-- Actions -->
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end">

                                <button type="button" class="btn btn-sm btn-soft-info me-2" data-bs-toggle="modal" data-bs-target="#studentModal{{ $student->user_id }}">
    <i class="fas fa-eye"></i>
</button>


                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">No Students Found</h5>
                                    <p class="text-muted small">Add your first student to get started</p>
                                    @if(request('search') || request('status'))
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        Clear filters
                                    </a>
                                    @else
                                    <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> Add Student
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @foreach($students as $student)
<!-- Student Detail Modal -->
<div class="modal fade" id="studentModal{{ $student->user_id }}" tabindex="-1" aria-labelledby="studentModalLabel{{ $student->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel{{ $student->user_id }}">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">Full Name</dt>
          <dd class="col-sm-8">{{ $student->first_name }} {{ $student->last_name }}</dd>

          <dt class="col-sm-4">Email</dt>
          <dd class="col-sm-8">{{ $student->email }}</dd>

          <dt class="col-sm-4">Phone Number</dt>
          <dd class="col-sm-8">{{ $student->phone_number ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Student Number</dt>
          <dd class="col-sm-8">{{ $student->studentDetail->student_number ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Course</dt>
          <dd class="col-sm-8">{{ $student->studentDetail->course ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Faculty</dt>
          <dd class="col-sm-8">{{ $student->studentDetail->faculty ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Address</dt>
          <dd class="col-sm-8">{{ $student->address ?? 'N/A' }}</dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

            @if($students->hasPages())
            <div class="card-footer bg-transparent border-0 pt-3">
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

@push('styles')
<style>
    .search-highlight {
        background-color: #ffeb3b;
        padding: 0 2px;
        border-radius: 2px;
        font-weight: bold;
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
        const tableRows = document.querySelectorAll('tbody tr');

        // Function to highlight text matches
        function highlightText(element, searchTerm) {
            if (!element || !searchTerm) return;

            const text = element.textContent;
            const lowerText = text.toLowerCase();
            let startIndex = 0;

            // If the search term isn't found in this element, return
            if (!lowerText.includes(searchTerm.toLowerCase())) return;

            // Clear the element's content
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }

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

        // Function to filter table rows based on search input
        function filterStudents() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const searchFeedback = document.getElementById('searchFeedback');
            const resultCount = document.getElementById('resultCount');

            let visibleCount = 0;

            // Remove any existing highlights first
            document.querySelectorAll('.search-highlight').forEach(el => {
                const parent = el.parentNode;
                parent.replaceChild(document.createTextNode(el.textContent), el);
                // Normalize the parent to merge adjacent text nodes
                parent.normalize();
            });

            tableRows.forEach(row => {
                if (row.querySelector('.empty-state')) {
                    // Skip the "No Students Found" row
                    return;
                }

                const studentNameEl = row.querySelector('.fw-medium');
                const studentIdEl = row.querySelector('small.text-muted');
                const emailEl = row.querySelector('.text-primary');
                const phoneEl = row.querySelectorAll('small.text-muted')[0];
                const addressEl = row.querySelectorAll('small.text-muted')[1];

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

                row.style.display = matchesSearch ? '' : 'none';

                // Highlight matching text if there's a search term
                if (matchesSearch && searchTerm) {
                    highlightText(studentNameEl, searchTerm);
                    highlightText(studentIdEl, searchTerm);
                    highlightText(emailEl, searchTerm);
                    highlightText(phoneEl, searchTerm);
                    highlightText(addressEl, searchTerm);
                    visibleCount++;
                }
            });

            // Show or hide the "No Students Found" message
            const visibleRows = Array.from(tableRows).filter(row =>
                !row.querySelector('.empty-state') && row.style.display !== 'none'
            );

            const emptyStateRow = document.querySelector('.empty-state')?.closest('tr');
            if (emptyStateRow) {
                emptyStateRow.style.display = visibleRows.length === 0 ? '' : 'none';
            }

            // Update search feedback and error state
            const searchError = document.getElementById('searchError');
            if (searchFeedback && resultCount) {
                if (searchTerm) {
                    resultCount.textContent = visibleCount;

                    // Show either feedback or error message based on results
                    if (visibleCount > 0) {
                        searchFeedback.style.display = 'block';
                        if (searchError) searchError.style.display = 'none';
                    } else {
                        searchFeedback.style.display = 'none';
                        if (searchError) searchError.style.display = 'block';
                    }
                } else {
                    searchFeedback.style.display = 'none';
                    if (searchError) searchError.style.display = 'none';
                }
            }
        }

        // Add event listeners
        if (searchInput) {
            searchInput.addEventListener('input', filterStudents);

            // Clear search when X button is clicked
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    filterStudents();

                    // Explicitly hide the search feedback and error message
                    const searchFeedback = document.getElementById('searchFeedback');
                    const searchError = document.getElementById('searchError');
                    if (searchFeedback) {
                        searchFeedback.style.display = 'none';
                    }
                    if (searchError) {
                        searchError.style.display = 'none';
                    }
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
    });
</script>
@endpush
@endsection
