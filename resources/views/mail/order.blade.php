<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .order-table th,
        .order-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <p>Date: {{$entryDate}}</p>
        <h3 class="mb-4">Here's the order detail:</h3>
        <table class="order-table">
            <tr>
                <th></th>
                <th>Name</th>
                <th> - </th>
                <th>Quantity</th>
            </tr>
            @foreach ($itemlist as $index=>$item)
            <tr>
                <td>{{$index+1}}</td>
                <td>{{$item->NAME}}</td>
                <td>{{$item->NAME2}}</td>
                <td class="qty">{{$item->quantity}}</td>
            </tr>
            @endforeach
        </table>
        <p>If you have questions, please reach out to us.</p>
        <p>Thanks,</p>
        <div>The MOMO Station</div>
        {!! $signature !!}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>