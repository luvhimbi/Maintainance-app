@extends('layouts.AdminNavBar')
@section('page_title', 'Assign Task to Technician')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Assign Task to Technician</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="issue_id" class="col-md-4 col-form-label text-md-right">Issue</label>
                            <div class="col-md-6">
                                <select id="issue_id" class="form-control @error('issue_id') is-invalid @enderror" name="issue_id" required>
                                    <option value="">Select an Issue</option>
                                    @foreach($tasks as $task)
                                        <option value="{{ $task->task_id }}" {{ old('issue_id') == $task->task_id ? 'selected' : '' }}>
                                            Task#{{ $task->task_id }} - {{ $task->issue->issue_description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('issue_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="assignee_id" class="col-md-4 col-form-label text-md-right">Technician</label>
                            <div class="col-md-6">
                                <select id="assignee_id" class="form-control @error('assignee_id') is-invalid @enderror" name="assignee_id" required>
                                    <option value="">Select a Technician</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{  $tech->user_id }}" {{ old('assignee_id') == $tech->user_id ? 'selected' : '' }}>
                                            {{  $tech->username }} 
                                        </option>
                                    @endforeach
                                </select>
                                @error('assignee_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="expected_completion" class="col-md-4 col-form-label text-md-right">Expected Completion</label>
                            <div class="col-md-6">
                                <input id="expected_completion" type="datetime-local" class="form-control @error('expected_completion') is-invalid @enderror" name="expected_completion" value="{{ old('expected_completion') }}" required>
                                @error('expected_completion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                      

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Assign Task
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection