@extends('layouts.app') {{-- Adjust to match your app configuration layouts theme wrapper --}}

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">Invoice Number Structural Pattern Migration Matrix</h1>
            <p class="text-muted mb-0">Review system transformation previews matching modern compliance rules before changing production row identities.</p>
        </div>

        <form action="{{ route('invoices.migration.run') }}" method="POST" onsubmit="return confirm('CRITICAL ACTION WARNING: Are you completely certain you wish to execute batch row mutations? Double-verify fiscal sequences before continuing.');">
            @csrf
            <button type="submit" class="btn btn-danger btn-lg px-4 font-weight-bold shadow-sm">
                ⚡ Commit System Conversion Sequence
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow mb-4" role="alert">
            <strong>Execution Completed:</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-left-danger shadow mb-4" role="alert">
            <strong>Halted Operations Error:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary">Chronological Registry Real-Time Execution Map</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-bordered table-striped m-0" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white sticky-top">
                        <tr>
                            <th># ID</th>
                            <th>Target Client Name</th>
                            <th>Invoice Date</th>
                            <th>Invoice Total</th>
                            <th>Current Reference Value</th>
                            <th>Target Schema Outcome</th>
                            <th>Migration Status Label</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $row)
                            <tr class="{{ !$row['is_old_pattern'] ? 'table-success-light' : '' }}">
                                <td><code>#{{ $row['id'] }}</code></td>
                                <td>{{ $row['name'] }}</td>
                                <td><span class="badge badge-secondary p-2 font-weight-normal">{{ $row['invoice_date'] }}</span></td>
                                <td class="font-weight-bold">₹{{ number_format($row['total_amount'], 2) }}</td>
                                <td>
                                    <span class="badge {{ $row['is_old_pattern'] ? 'badge-warning text-dark' : 'badge-success' }} px-3 py-2">
                                        {{ $row['current_no'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="{{ $row['is_old_pattern'] ? 'text-danger h6' : 'text-success' }}">
                                        {{ $row['preview_no'] }}
                                    </strong>
                                </td>
                                <td>
                                    @if($row['is_old_pattern'])
                                        <span class="text-danger font-weight-bold">⚠️ Dynamic Remap Pending</span>
                                    @else
                                        <span class="text-success font-weight-bold">✓ Confirmed Compliant</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Sticky header styles for smooth long scrolling maps */
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1020;
    }
    .table-success-light {
        background-color: rgba(40, 167, 69, 0.12) !important;
    }
</style>
@endsection
