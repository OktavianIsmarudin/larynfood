<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background: #F8F9FA;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            border-bottom: 3px solid #21489d;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info h1 {
            color: #21489d;
            font-size: 32px;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #6C757D;
            font-size: 14px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            color: #212529;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .invoice-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-box {
            background: #F8F9FA;
            padding: 20px;
            border-radius: 8px;
        }

        .info-box h3 {
            color: #21489d;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #212529;
            font-size: 14px;
            line-height: 1.6;
            margin: 5px 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table thead {
            background: #21489d;
            color: white;
        }

        .invoice-table th {
            padding: 15px;
            text-align: left;
            font-size: 14px;
            text-transform: uppercase;
        }

        .invoice-table td {
            padding: 15px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
        }

        .invoice-table tbody tr:last-child td {
            border-bottom: 2px solid #21489d;
        }

        .text-right {
            text-align: right;
        }

        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .summary-box {
            width: 350px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 14px;
        }

        .summary-row.total {
            border-top: 2px solid #21489d;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #21489d;
        }

        .payment-status {
            background: #D1FAE5;
            color: #065F46;
            padding: 8px 15px;
            border-radius: 6px;
            display: inline-block;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .footer {
            border-top: 2px solid #E5E7EB;
            padding-top: 20px;
            text-align: center;
            color: #6C757D;
            font-size: 12px;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>🍽️ Laryn</h1>
                <p>Sistem Inventory & Keuangan</p>
                <p>Email: info@laryn.com</p>
                <p>Phone: +62 812-3456-7890</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <span class="payment-status">DIBAYAR</span>
            </div>
        </div>

        <!-- Invoice Meta -->
        <div class="invoice-meta">
            <div class="info-box">
                <h3>Invoice Kepada:</h3>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->customer_email }}</p>
                <p>{{ $order->customer_phone }}</p>
                <p style="margin-top: 10px;">{{ $order->shipping_address }}</p>
            </div>

            <div class="info-box">
                <h3>Detail Invoice:</h3>
                <p><strong>Nomor Invoice:</strong> {{ $order->order_number }}</p>
                <p><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d F Y') }}</p>
                <p><strong>Tanggal Bayar:</strong> {{ $order->paid_at->format('d F Y') }}</p>
                <p><strong>Metode Pembayaran:</strong> {{ ucfirst($order->midtrans_payment_type ?? 'Midtrans') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <strong>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row">
                    <span>Ongkos Kirim:</span>
                    <strong>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row total">
                    <span>Total Pembayaran:</span>
                    <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($order->notes)
        <div class="info-box" style="margin-bottom: 30px;">
            <h3>Catatan:</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih atas pembelian Anda!</strong></p>
            <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan.</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} Laryn. All rights reserved.</p>
        </div>

        <!-- Print Button -->
        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="background: #21489d; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold;">
                🖨️ Cetak Invoice
            </button>
        </div>
    </div>
</body>
</html>
