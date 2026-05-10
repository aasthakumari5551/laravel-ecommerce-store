<!DOCTYPE html>
<html>
<head>
    <title>E-Commerce Store</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial;
        }

        body{
            background:#f5f5f5;
        }

        .navbar{
            background:#131921;
            color:white;
            padding:20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .logo{
            font-size:28px;
            font-weight:bold;
        }

        .nav-links a{
            color:white;
            text-decoration:none;
            margin-left:20px;
            font-size:18px;
        }

        .top-section{
            padding:20px;
        }

        .add-btn{
            background:#ff9900;
            border:none;
            padding:10px 15px;
            cursor:pointer;
            border-radius:5px;
            font-size:16px;
        }

        .container{
            display:flex;
            flex-wrap:wrap;
            gap:20px;
            padding:20px;
        }

        .card{
            background:white;
            width:260px;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
            transition:0.3s;
        }

        .card:hover{
            transform:scale(1.03);
        }

        .card img{
            width:100%;
            height:220px;
            object-fit:cover;
        }

        .card-body{
            padding:15px;
        }

        .card-body h2{
            margin-bottom:10px;
            font-size:22px;
        }

        .card-body p{
            margin-bottom:10px;
        }

        .price{
            color:green;
            font-size:22px;
            font-weight:bold;
        }

        .stock{
            color:#555;
        }

        .btn{
            padding:8px 12px;
            border:none;
            cursor:pointer;
            margin-top:10px;
            border-radius:5px;
        }

        .edit-btn{
            background:#007bff;
            color:white;
        }

        .delete-btn{
            background:red;
            color:white;
        }

    </style>
</head>

<body>

    <div class="navbar">

        <div class="logo">
            MyShop
        </div>

        <div class="nav-links">
            <a href="/">Home</a>
            <a href="/products/create">Add Product</a>
        </div>

    </div>

    <div class="top-section">

    <form action="/" method="GET">

        <input type="text"
               name="search"
               placeholder="Search products..."
               style="padding:10px; width:300px;">

        <button type="submit" class="add-btn">
            Search
        </button>

    </form>

    <br>

    <a href="/products/create">
        <button class="add-btn">
            Add New Product
        </button>
    </a>

</div>

    <div class="container">

        @foreach($products as $product)

            <div class="card">

                <img src="{{ $product->image }}">

                <div class="card-body">

                    <h2>{{ $product->name }}</h2>

                    <p>{{ $product->description }}</p>

                    <p class="price">
                        ₹{{ $product->price }}
                    </p>

                    <p class="stock">
                        Stock: {{ $product->stock }}
                    </p>

                    <a href="/add-to-cart/{{ $product->id }}">
                        <button class="btn">
                            Add To Cart
                        </button>
                    </a>

                    <a href="/products/{{ $product->id }}/edit">
                        <button class="btn edit-btn">
                            Edit
                        </button>
                    </a>

                    <form action="/products/{{ $product->id }}" method="POST">

                        @csrf
                        @method('DELETE')

                        <button class="btn delete-btn" type="submit">
                            Delete
                        </button>

                    </form>

                </div>

            </div>

        @endforeach

    </div>

</body>
</html>