@extends('layouts.AdminNavBar')

@section('page_title', 'Assign Task to Technician')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Assign Task #{{ $task->task_id }}</h5>
                </div>
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->task_id }}">

                        <div class="mb-4">
                            <h5 class="mb-3">Task Details</h5>
                            <div class="card bg-light p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Issue Description:</strong></p>
                                        <p>{{ $task->issue->issue_description }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Reported By:</strong> {{ $task->issue->reporter->username ?? 'N/A'}}</p>
                                        <p><strong>Priority:</strong> 
                                            <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'success') }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </p>
                                        <p><strong>Status:</strong> {{ ucfirst($task->issue_status) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                      
                        <div class="mb-3">
                            <label for="assignee_id" class="form-label">Select Technician *</label>
                            <select id="assignee_id" class="form-select @error('assignee_id') is-invalid @enderror" name="assignee_id" required>
                                <option value="">-- Select Technician --</option>
                                @forelse($technicians as $tech)
                                    <option value="{{ $tech->user_id }}" 
                                        @selected(old('assignee_id', $task->assignee_id ?? null) == $tech->user_id)>
                                        {{ $tech->username }} ({{ $tech->email }})
                                        @if($tech->phone)
                                            - {{ $tech->phone_number }}
                                        @endif
                                    </option>
                                @empty
                                    <option value="" disabled>No technicians available</option>
                                @endforelse
                            </select>
                            
                            @error('assignee_id')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="expected_completion" class="form-label">Expected Completion Date *</label>
                            <input id="expected_completion" type="datetime-local" 
                                   class="form-control @error('expected_completion') is-invalid @enderror" 
                                   name="expected_completion" 
                                   value="{{ old('expected_completion') }}" 
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                            @error('expected_completion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i> Assign Technician
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection