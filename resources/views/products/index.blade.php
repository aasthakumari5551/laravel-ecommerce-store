<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>

    <h1>All Products</h1>

    @foreach($products as $product)

        <div style="border:1px solid black; padding:10px; margin:10px; width:300px;">

            <h2>{{ $product->name }}</h2>

            <img src="{{ $product->image }}" width="200">

            <p>{{ $product->description }}</p>

            <p>Price: ₹{{ $product->price }}</p>

            <p>Stock: {{ $product->stock }}</p>

            <a href="/products/{{ $product->id }}/edit">
                <button>Edit</button>
            </a>

            <form action="/products/{{ $product->id }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit">Delete</button>
            </form>

        </div>

    @endforeach

</body>
</html>