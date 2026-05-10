<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>

<h1>Edit Product</h1>

<form action="/products/{{ $product->id }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $product->name }}"><br><br>

    <textarea name="description">{{ $product->description }}</textarea><br><br>

    <input type="number" name="price" value="{{ $product->price }}"><br><br>

    <input type="number" name="stock" value="{{ $product->stock }}"><br><br>

    <input type="text" name="image" value="{{ $product->image }}"><br><br>

    <button type="submit">Update Product</button>
</form>

</body>
</html>