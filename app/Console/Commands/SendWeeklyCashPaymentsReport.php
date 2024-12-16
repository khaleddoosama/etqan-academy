<?php

namespace App\Console\Commands;

use App\Exports\CashPaymentsExport;
use App\Exports\InstallmentPaymentsExport;
use App\Exports\SummaryPaymentsExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendWeeklyCashPaymentsReport extends Command
{
    protected $signature = 'report:weekly-payments';
    protected $description = 'Export weekly cash payments and send via email';

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
            Excel::store(new CashPaymentsExport, 'reports/weekly_cash_payments.xlsx');
            Excel::store(new InstallmentPaymentsExport, 'reports/weekly_installment_payments.xlsx');
            Excel::store(new SummaryPaymentsExport, 'reports/weekly_summary_payments.xlsx');

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
            Log::error('Error sending weekly cash payments report: ' . $e->getMessage());
            $this->error('Failed to send weekly cash payments report.');
        }
    }
}
