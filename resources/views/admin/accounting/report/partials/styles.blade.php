{{-- Custom Styles for Reports --}}
<style>
    .report-card {
        transition: transform 0.2s ease-in-out;
        border-radius: 10px;
    }

    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 20px;
    }

    .export-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        margin-bottom: 20px;
    }

    .table-combined {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .filter-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .export-btn {
        border: 2px solid white;
        background: transparent;
        color: white;
        transition: all 0.3s ease;
    }    .export-btn:hover {
        background: white;
        color: #667eea;
    }

    #filter-status {
        background: #e3f2fd;
        padding: 8px 12px;
        border-radius: 5px;
        border-left: 4px solid #2196f3;
        margin-top: 10px;
    }

    #filter-status .fas {
        color: #2196f3;
        margin-right: 5px;
    }

    /* URL-friendly filter highlighting */
    .filter-applied {
        border-color: #2196f3 !important;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25) !important;
    }

    /* Loading indicators */
    .loading {
        position: relative;
        opacity: 0.7;
        pointer-events: none;
    }

    .loading-overlay, .chart-loading-overlay, .table-loading-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .loading-overlay i, .chart-loading-overlay i, .table-loading-overlay i {
        font-size: 24px;
        color: #2196f3;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Component update animations */
    .small-box {
        transition: all 0.3s ease;
    }

    .small-box.updating {
        transform: scale(0.98);
        opacity: 0.8;
    }

    .card.updating {
        transform: translateY(2px);
        box-shadow: 0 1px 5px rgba(0,0,0,0.1);
    }

    /* Progress bar for updates */
    .progress-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        min-width: 300px;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e0e0e0;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #2196f3, #21cbf3);
        border-radius: 3px;
        transition: width 0.3s ease;
        position: relative;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-text {
        font-size: 12px;
        color: #666;
        text-align: center;
        font-weight: 500;
    }
</style>
