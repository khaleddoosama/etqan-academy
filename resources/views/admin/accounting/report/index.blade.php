@extends('admin.master')

@section('title')
{{ __('attributes.accounting_reports') }}
@endsection

@section('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@include('admin.accounting.report.partials.styles')
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <x-custom.header-page title="{{ __('attributes.accounting_reports') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            
            <!-- Summary Statistics -->
            @include('admin.accounting.report.partials.statistics')
            
            <!-- Filters Section -->
            @include('admin.accounting.report.partials.filters')
            
            <!-- Export Section -->
            @include('admin.accounting.report.partials.export-section')

            <!-- Charts Section -->
            @include('admin.accounting.report.partials.charts')

            <!-- Combined Income Data Table -->
            @include('admin.accounting.report.partials.data-table')

        </div>
    </section>
</div>
@endsection

@section('scripts')
@include('admin.accounting.report.partials.scripts')
@endsection
