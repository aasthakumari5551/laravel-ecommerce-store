<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>

    <h1>All Products</h1>

    @foreach($products as $product)
        <div style="border:1px solid black; padding:10px; margin:10px;">
            <h2>{{ $product->name }}</h2>

            <p>{{ $product->description }}</p>

            <p>Price: ₹{{ $product->price }}</p>

            <p>Stock: {{ $product->stock }}</p>
        </div>
    @endforeach

</body>
</html>