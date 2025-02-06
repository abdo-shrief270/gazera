<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{__('messages.invoice') .' '.'Gz'.sprintf("%06d", $invoice->invoice_number)}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .invoice-header {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .summary-card {
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .footer {
            background: #e9ecef;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-header, .summary-card, .table, .footer {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="invoice-header">
        <h2 class="mb-0">{{__('messages.invoice') .' '.'Gz'.sprintf("%06d", $invoice->invoice_number)}}</h2>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="summary-card">
                <h4 class="text-primary">شركة الجزيرة للتوريدات الكهربائية</h4>
                <p>س.ت 2257082 ب.ض 9511</p>
                <p>رقم التسجيل الضريبي: <span class="fw-bold">815-413-202</span></p>
                <p>رقم الهاتف: <span class="fw-bold">0223918948</span></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card text-md-start">
                <h5>اسم العميل: <span class="fw-bold text-success">{{$invoice->customer->name}}</span></h5>
                <h5>رقم التسجيل الضريبي: <span dir="ltr" class="fw-bold text-end text-success">{{$invoice->customer->tax_number}}</span></h5>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-hover mt-4">
        <thead class="table-dark">
        <tr>
            <th>الكود</th>
            <th>المنتج</th>
            <th>الوصف</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->details as $detail)
            <tr>
                <td>{{$detail->product->code}}</td>
                <td>{{$detail->product->name}}</td>
                <td>{{$detail->description}}</td>
                <td>{{$detail->unit_price}}</td>
                <td>{{$detail->quantity}}</td>
                <td>{{$detail->subtotal}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-danger float-left mt-3 mr-2 no-print" id="print_Button" onclick="printDiv()">
                <i class="mdi mdi-printer ml-1"></i> طباعة
            </button>
        </div>
        <div class="col-md-6">
            <div class="summary-card">
                <table class="table">
                    <tr>
                        <td>السعر الأساسي</td>
                        <td class="text-end fw-bold">{{$invoice->subtotal}} ج.م</td>
                    </tr>
                    <tr>
                        <td>{{__('messages.total_fee')}}</td>
                        <td class="text-end fw-bold">{{$invoice->total_price-$invoice->subtotal}} ج.م</td>
                    </tr>
                    <tr class="table-primary">
                        <td class="fw-bold">السعر الكلي</td>
                        <td class="fw-bold text-end">{{$invoice->total_price}} ج.م</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>تم إنشاء الفاتورة بواسطة الكمبيوتر وهي صالحة بدون توقيع أو ختم.</p>
    </div>
</div>

<script type="text/javascript">
    function printDiv(){
        var printContents = document.querySelector('.container').innerHTML; // Get only the invoice container
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents; // Replace the body content with the invoice content
        window.print(); // Trigger the print dialog
        document.body.innerHTML = originalContents; // Restore the original content
        window.location.reload(); // Reload the page to restore functionality
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
