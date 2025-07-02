<?php

namespace App\Console\Commands;

use App\Exports\CashPaymentsExport;
use App\Exports\InstallmentPaymentsExport;
use App\Exports\SummaryPaymentsExport;
use App\Services\PaymentDetailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendWeeklyCashPaymentsReport extends Command
{
    protected $signature = 'report:weekly-payments';
    protected $description = 'Export weekly cash payments and send via email';


    // constructor
    public function __construct(protected PaymentDetailService $paymentDetailService)
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure the reports directory exists
        $reportsDir = storage_path('app/public/reports');
        if (!file_exists($reportsDir)) {
            mkdir($reportsDir, 0755, true);
        }

        $cashFilePath  = storage_path('app/public/reports/weekly_cash_payments.xlsx');
        $installmentFilePath = storage_path('app/public/reports/weekly_installment_payments.xlsx');
        $summaryFilePath = storage_path('app/public/reports/weekly_summary_payments.xlsx');

        try {
            // Generate the Excel file
            Excel::store(new CashPaymentsExport($this->paymentDetailService), 'reports/weekly_cash_payments.xlsx');
            Excel::store(new InstallmentPaymentsExport($this->paymentDetailService), 'reports/weekly_installment_payments.xlsx');
            Excel::store(new SummaryPaymentsExport($this->paymentDetailService), 'reports/weekly_summary_payments.xlsx');

            // Send the email
            Mail::send([], [], function ($message) use ($cashFilePath, $installmentFilePath, $summaryFilePath) {
                $message->to('kahrabaobama1011@gmail.com')
                    ->subject('Weekly Payments Report')
                    ->attach($cashFilePath, [
                        'as' => 'weekly_cash_payments-' . date('Y-m-d') . '.xlsx',
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->attach($installmentFilePath, [
                        'as' => 'weekly_installment_payments-' . date('Y-m-d') . '.xlsx',
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->attach($summaryFilePath, [
                        'as' => 'weekly_summary_payments-' . date('Y-m-d') . '.xlsx',
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
            });

            $this->info('Weekly cash payments report sent successfully.');
        } catch (\Exception $e) {
            // Log the exception and show an error message
            Log::error('Error sending weekly payments report: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            $this->error('Failed to send weekly payments report.');
        }
    }
}
