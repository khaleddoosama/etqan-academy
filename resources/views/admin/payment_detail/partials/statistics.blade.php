{{-- Instapay Statistics --}}
<div class="row mb-3">
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $statistics['pending'] }}</h3>
                <p>Pending Instapay</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="#" onclick="$('#filter-gateway').val('instapay'); $('#filter-status').val('pending'); $('#table').DataTable().draw();" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $statistics['approved'] }}</h3>
                <p>Approved Instapay</p>
            </div>
            <div class="icon">
                <i class="fas fa-check"></i>
            </div>
            <a href="#" onclick="$('#filter-gateway').val('instapay'); $('#filter-status').val('paid'); $('#table').DataTable().draw();" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $statistics['total'] }}</h3>
                <p>Total Instapay</p>
            </div>
            <div class="icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <a href="#" onclick="$('#filter-gateway').val('instapay'); $('#filter-status').val(''); $('#table').DataTable().draw();" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $statistics['fawaterak'] }}</h3>
                <p>Fawaterak Payments</p>
            </div>
            <div class="icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <a href="#" onclick="$('#filter-gateway').val('fawaterak'); $('#filter-status').val(''); $('#table').DataTable().draw();" class="small-box-footer">
                View All <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
