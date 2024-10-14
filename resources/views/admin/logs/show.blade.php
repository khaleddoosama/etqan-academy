@extends('admin.master')
@section('title')
    {{ __('attributes.logs') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.logs') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">All Logs For {{ $file }}</h3>
                                <div class="float-right">
                                    <input type="text" id="logSearchInput" class="form-control"
                                        placeholder="Search logs..." onkeyup="searchLogs()">
                                </div>
                            </div>
                            <div class="card-body">
                                <pre id="logContent" style="max-height: 600px; overflow-y: scroll;">{{ $log }}</pre>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
@endsection

@section('scripts')
    <script>
        function searchLogs() {
            // Get the input field and filter
            var input = document.getElementById("logSearchInput");
            var filter = input.value.toLowerCase();
            var logContent = document.getElementById("logContent");

            // Get the lines of the log
            var lines = logContent.innerText.split("\n");

            // Initialize a variable to store the filtered lines
            var filteredLines = "";

            // Loop through the lines and check if the search term exists
            for (var i = 0; i < lines.length; i++) {
                if (lines[i].toLowerCase().includes(filter)) {
                    // Highlight the matching text with a custom color (red in this example)
                    var highlightedLine = lines[i].replace(new RegExp(filter, 'gi'), (match) =>
                        `<span style="background-color: yellow; color: red;">${match}</span>`);
                    filteredLines += highlightedLine + "\n";
                } else {
                    // If no match, just append the line without changes
                    filteredLines += lines[i] + "\n";
                }
            }

            // Update the content of the log with the filtered lines
            logContent.innerHTML = `${filteredLines}`;
        }
    </script>
@endsection
