<!DOCTYPE html>
<html>
<head>
    <title>Order Created</title>
</head>
<body>
    <h1>Order Created!</h1>
    <p>Dear {{ $order->buyer->name }},</p>
    <p>Your order #{{ $order->id }} has been successfully created.</p>
    <p>Details:</p>
    <ul>
        <li>Service: {{ $order->service->title }}</li>
        <li>Price: {{ $order->price }}</li>
        <li>Status: {{ $order->status }}</li>
    </ul>
    <p>Thank you for your purchase!</p>
</body>
</html>
