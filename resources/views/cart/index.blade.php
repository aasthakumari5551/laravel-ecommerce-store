<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>

    <style>

        body{
            font-family:Arial;
            background:#f5f5f5;
            margin:0;
            padding:20px;
        }

        h1{
            margin-bottom:20px;
        }

        .cart-container{
            display:flex;
            flex-direction:column;
            gap:20px;
        }

        .cart-item{
            background:white;
            display:flex;
            gap:20px;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        .cart-item img{
            width:150px;
            height:150px;
            object-fit:cover;
        }

        .details h2{
            margin-bottom:10px;
        }

        .price{
            color:green;
            font-size:22px;
            font-weight:bold;
        }

        .btn{
            padding:8px 12px;
            cursor:pointer;
        }

    </style>

</head>

<body>

    <h1>My Cart</h1>

    @php
        $total = 0;
    @endphp

    <div class="cart-container">

        @foreach($cartItems as $item)

            @php
                $total += $item->product->price * $item->quantity;
            @endphp

            <div class="cart-item">

                <img src="{{ $item->product->image }}">

                <div class="details">

                    <h2>{{ $item->product->name }}</h2>

                    <p>{{ $item->product->description }}</p>

                    <p class="price">
                        ₹{{ $item->product->price }}
                    </p>

                    <div style="margin-top:10px;">

                        <a href="/cart/decrease/{{ $item->id }}">
                            <button class="btn">-</button>
                        </a>

                        <span style="margin:10px;">
                            {{ $item->quantity }}
                        </span>

                        <a href="/cart/increase/{{ $item->id }}">
                            <button class="btn">+</button>
                        </a>

                    </div>

                    <br>

                    <a href="/cart/remove/{{ $item->id }}">
                        <button style="background:red; color:white; padding:8px;">
                            Remove
                        </button>
                    </a>

                </div>

            </div>

        @endforeach

        <h2 style="margin-top:30px;">
            Total Price: ₹{{ $total }}
        </h2>

        <br><br>

        <a href="/checkout">
            <button style="padding:12px; background:green; color:white; border:none;">
                Proceed To Checkout
            </button>
        </a>

    </div>

</body>
</html>