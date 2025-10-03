{{-- Export Reports Section --}}
<div class="export-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4><i class="fas fa-download"></i> Export Reports</h4>
            <p class="mb-0">Download comprehensive accounting reports in various formats</p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <button class="btn export-btn" onclick="exportReport('xlsx')">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button class="btn export-btn" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button class="btn export-btn" onclick="exportReport('json')">
                    <i class="fas fa-file-code"></i> JSON
                </button>
            </div>
        </div>
    </div>
</div>
