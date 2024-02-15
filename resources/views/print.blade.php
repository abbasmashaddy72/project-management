<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fileName }}</title>

    <!-- Include Water.css -->
    <link rel="stylesheet" href="{{ asset('css/water.css') }}">
</head>

<body>
    <h2>{{ $invoice->invoice_number }}</h2>
    <hr>

    <div>
        <h3>Bill To</h3>
        <p>{{ $invoice->client->name }}<br> {{ $invoice->client->email }}<br> {{ $invoice->client->contact_no }}<br>
            {{ $invoice->client->address }}
        </p>
    </div>

    <div>
        <h3>Invoice Details</h3>
        <p>Invoice Date: {{ $invoice->issued_on->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>VAT</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit_price }}</td>
                    <td>{{ $item->vat }}</td>
                    <td>{{ $item->currency }} {{ $item->amount }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4">Sub Total</td>
                <td>{{ $invoice->currency }} {{ $invoice->subtotal }}</td>
            </tr>
            <tr>
                <td colspan="4">VAT</td>
                <td>{{ $invoice->vat }}</td>
            </tr>
            <tr>
                <td colspan="4">Total</td>
                <td>{{ $invoice->currency }} {{ $invoice->total }}</td>
            </tr>
        </tbody>
    </table>

    <p>Thank you for your cooperation, I hope our cooperation can continue for the next month.</p>

    <div>
        <h3>Project Details:</h3>
        <p>{{ $invoice->project->name }}</p>
        {!! $invoice->project->description !!}
    </div>

    <div>
        @if ($invoice->ach_transfer == true)
            <h3>USD account details (for ACH transfer):</h3>
            <p>Account Holder: <span>{{ $invoice->worker_name }}</span></p>
            <p>ACH and Wire routing number: <span>{{ $invoice->ach_routing_number }}</span></p>
            <p>Account Number: <span>{{ $invoice->ach_account_number }}</span></p>
            <p>Address: <span>{{ $invoice->ach_account_address }}</span></p>
            <h3>Or, Paypal:</h3>
        @endif
        <p><a href="{{ $invoice->payment_link }}">{{ $invoice->payment_link }}</a></p>
        <p>{{ $invoice->summary }}</p>
    </div>
</body>

</html>
