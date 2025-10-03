{{-- Summary Statistics Cards --}}
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success report-card">
            <div class="inner">
                <h3 id="stat-total-income">{{ number_format($summary['total_income'], 2) }}</h3>
                <p>Total Income</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-up stat-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger report-card">
            <div class="inner">
                <h3 id="stat-total-expenses">{{ number_format($summary['total_expenses'], 2) }}</h3>
                <p>Total Expenses</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-down stat-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info report-card">
            <div class="inner">
                <h3 id="stat-net-profit">{{ number_format($summary['net_profit'], 2) }}</h3>
                <p>Net Profit</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line stat-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning report-card">
            <div class="inner">
                <h3 id="stat-profit-margin">{{ $summary['profit_margin'] }}%</h3>
                <p>Profit Margin</p>
            </div>
            <div class="icon">
                <i class="fas fa-percentage stat-icon"></i>
            </div>
        </div>
    </div>
</div>
