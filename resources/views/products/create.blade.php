<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>

    <h1>Add Product</h1>

    <form action="/products" method="POST">
        @csrf

        <input type="text" name="name" placeholder="Product Name"><br><br>

        <textarea name="description" placeholder="Description"></textarea><br><br>

        <input type="number" name="price" placeholder="Price"><br><br>

        <input type="number" name="stock" placeholder="Stock"><br><br>

        <input type="text" name="image" placeholder="Image URL"><br><br>

        <button type="submit">Add Product</button>
    </form>

</body>
</html>