{{-- Accounting Entry Statistics --}}
<div class="row mb-3">
    <div class="col-md">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="stat-total-income-entries">{{ number_format($statistics['total_income_entries'] ?? 0, 2) }}</h3>
                <p>Total Income Entries</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <a href="#" onclick="event.preventDefault(); 
            $('#filter-type').val('income'); 
            updateFiltersAndTable(); 
            const url = new URL(window.location);
            url.searchParams.set('type', 'income');
            window.history.pushState({}, '', url);" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="stat-total-payments">{{ number_format($statistics['total_payments'] ?? 0, 2) }}</h3>
                <p>Total Payments</p>
            </div>
            <div class="icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <a href="{{ route('admin.payment_details.index', ['from_paid_at' => request('from_date'), 'to_paid_at' => request('to_date')]) }}" target="_blank" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="stat-total-income">{{ number_format($statistics['total_income'] ?? 0, 2) }}</h3>
                <p>Total Income</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <a href="#"onclick="event.preventDefault(); 
            $('#filter-type').val('income'); 
            updateFiltersAndTable(); 
            const url = new URL(window.location);
            url.searchParams.set('type', 'income');
            window.history.pushState({}, '', url);" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 id="stat-total-expenses">{{ number_format($statistics['total_expenses'] ?? 0, 2) }}</h3>
                <p>Total Expenses</p>
            </div>
            <div class="icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <a href="#" onclick="event.preventDefault(); 
            $('#filter-type').val('expense'); 
            updateFiltersAndTable(); 
            const url = new URL(window.location);
            url.searchParams.set('type', 'income');
            window.history.pushState({}, '', url);" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="stat-net-profit">{{ number_format($statistics['net_profit'] ?? 0, 2) }}</h3>
                <p>Net Profit</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <a href="#" onclick="$('#filter-type').val(''); updateFiltersAndTable();" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
    function setCurrentMonthFilter() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        $('#filter-from').val(firstDay.toISOString().split('T')[0]);
        $('#filter-to').val(lastDay.toISOString().split('T')[0]);
    }
</script>