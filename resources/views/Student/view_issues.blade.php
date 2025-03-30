@extends('Layouts.StudentNavbar')
@section('title', 'View All Issues')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">All Issues</h1>
            <p class="text-muted">Track and manage your reported maintenance requests</p>
        </div>
       
    </div>

    <!-- Filter Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light h-100 cursor-pointer hover-shadow">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 bg-primary rounded p-2 text-white">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Open Issues</div>
                        <div class="h5 mb-0">{{ $issues->where('issue_status', 'Open')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light h-100 cursor-pointer hover-shadow">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 bg-warning rounded p-2 text-dark">
                        <i class="fas fa-tools fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small">In Progress</div>
                        <div class="h5 mb-0">{{ $issues->where('issue_status', 'In Progress')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light h-100 cursor-pointer hover-shadow">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 bg-success rounded p-2 text-white">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Resolved</div>
                        <div class="h5 mb-0">{{ $issues->where('issue_status', 'Resolved')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light h-100 cursor-pointer hover-shadow">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 bg-danger rounded p-2 text-white">
                        <i class="fas fa-fire-alt fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small">High Urgency</div>
                        <div class="h5 mb-0">{{ $issues->where('urgency_level', 'High')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="issueSearch" class="form-control border-start-0" placeholder="Search issues...">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-wrap gap-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                        <select class="form-select" id="urgencyFilter">
                            <option value="">All Urgency Levels</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Issues List -->
    @if($issues->count() > 0)
        <div class="card shadow-sm border-0 rounded-3">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Issue Type</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Location</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issues as $issue)
                            <tr class="align-middle">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $iconClass = 'fa-question-circle';
                                            if ($issue->issue_type == 'Plumbing') {
                                                $iconClass = 'fa-faucet';
                                            } elseif ($issue->issue_type == 'Electrical') {
                                                $iconClass = 'fa-bolt';
                                            } elseif ($issue->issue_type == 'Furniture') {
                                                $iconClass = 'fa-chair';
                                            } elseif ($issue->issue_type == 'HVAC') {
                                                $iconClass = 'fa-temperature-high';
                                            } elseif ($issue->issue_type == 'Internet') {
                                                $iconClass = 'fa-wifi';
                                            } elseif ($issue->issue_type == 'Cleaning') {
                                                $iconClass = 'fa-broom';
                                            }
                                        @endphp
                                        <span class="me-2 text-secondary">
                                            <i class="fas {{ $iconClass }}"></i>
                                        </span>
                                        <span>{{ $issue->issue_type }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($issue->urgency_level == 'Low') bg-success
                                        @elseif($issue->urgency_level == 'Medium') bg-warning text-dark
                                        @elseif($issue->urgency_level == 'High') bg-danger
                                        @endif px-2 py-1">
                                        {{ $issue->urgency_level }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($issue->issue_status == 'Open') bg-primary
                                        @elseif($issue->issue_status == 'In Progress') bg-warning text-dark
                                        @elseif($issue->issue_status == 'Resolved') bg-success
                                        @endif px-2 py-1">
                                        {{ $issue->issue_status }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($issue->report_date)->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 text-secondary">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <span>{{ $issue->location->building_name }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('Student.issue_details', $issue->issue_id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $issues->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body py-5">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                    </div>
                    <h4>No Issues Found</h4>
                    <p class="text-muted">You haven't reported any maintenance issues yet.</p>
                    
                </div>
            </div>
          
        </div>
        <!-- Pagination -->

    @endif
</div>

<style>
    /* .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        transition: box-shadow 0.3s ease;
    } */
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('issueSearch');
    const statusFilter = document.getElementById('statusFilter');
    const urgencyFilter = document.getElementById('urgencyFilter');
    const tableRows = document.querySelectorAll('tbody tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const urgencyValue = urgencyFilter.value;
        
        tableRows.forEach(row => {
            const issueType = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const urgency = row.querySelector('td:nth-child(2)').textContent.trim();
            const status = row.querySelector('td:nth-child(3)').textContent.trim();
            const location = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            
            const matchesSearch = issueType.includes(searchTerm) || location.includes(searchTerm);
            const matchesStatus = statusValue === '' || status === statusValue;
            const matchesUrgency = urgencyValue === '' || urgency === urgencyValue;
            
            if (matchesSearch && matchesStatus && matchesUrgency) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    urgencyFilter.addEventListener('change', filterTable);
});
</script>
@endsection