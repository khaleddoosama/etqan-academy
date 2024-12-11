<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Invoice</h1>
        <p>Payment Successful</p>
    </div>
    <div class="details">
        <p><strong>User Name:</strong> {{ $payment->user->name }}</p>
        <p><strong>Course:</strong> {{ $payment->course->title }}</p>
        <p><strong>Payment Type:</strong> {{ $payment->payment_type }}</p>
        {{-- <p><strong>Amount:</strong> {{ $payment->amount }}</p> --}}
        <p><strong>Date:</strong> {{ $payment->created_at }}</p>
    </div>
    <div class="footer">
        <p>Thank you for your payment!</p>
    </div>
</body>

</html>
