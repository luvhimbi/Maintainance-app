@extends('layouts.AdminNavBar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Campus Locations</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Location
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Building</th>
                                    <th>Coordinates</th>
                                    <th>Floor</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($buildings as $building)
                                    @foreach($building->floors as $floor)
                                        @php
                                            $roomCount = $floor->rooms->count();
                                        @endphp

                                        @foreach($floor->rooms as $room)
                                            <tr>
                                                @if($loop->first && $loop->parent->first)
                                                    <td rowspan="{{ $building->floors->sum(function($f) { return $f->rooms->count(); }) }}">
                                                        {{ $building->building_name }}
                                                    </td>
                                                    <td rowspan="{{ $building->floors->sum(function($f) { return $f->rooms->count(); }) }}">
                                                        {{ number_format($building->latitude, 6) }}, {{ number_format($building->longitude, 6) }}
                                                    </td>
                                                @endif

                                                @if($loop->first)
                                                    <td rowspan="{{ $roomCount }}">
                                                        Floor {{ $floor->floor_number }}
                                                    </td>
                                                @endif

                                                <td>Room {{ $room->room_number }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No locations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .table td {
        padding: 1rem;
        vertical-align: middle;
    }
    .border-right {
        border-right: 1px solid #e9ecef;
    }
    .building-icon, .floor-icon, .room-icon {
        width: 30px;
        text-align: center;
    }
    .coordinates {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
    }
    .alert {
        border: none;
        border-radius: 8px;
    }
    .btn-light {
        background-color: rgba(255,255,255,0.2);
        border: none;
    }
    .btn-light:hover {
        background-color: rgba(255,255,255,0.3);
    }
</style>
@endpush
