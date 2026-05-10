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
            border-radius:10px;
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

        @forelse(($cartItems ?? []) as $item)

            @php
                $product = $item->product;

                $total += ($product?->price ?? 0) * $item->quantity;
            @endphp

            @if($product)

                <div class="cart-item">

                    <img
                        src="{{ $product->image ?? 'https://via.placeholder.com/150' }}"
                        alt="{{ $product->name }}"
                    >

                    <div class="details">

                        <h2>{{ $product->name }}</h2>

                        <p>
                            {{ $product->description ?? 'No description available.' }}
                        </p>

                        <p class="price">
                            ₹{{ $product->price }}
                        </p>

                        <div style="margin-top:10px;">

                            <button class="btn">-</button>

                            <span style="margin:10px;">
                                {{ $item->quantity }}
                            </span>

                            <button class="btn">+</button>

                        </div>

                        <br>

                        <button
                            style="background:red; color:white; padding:8px;"
                        >
                            Remove
                        </button>

                    </div>

                </div>

            @endif

        @empty

            <div
                style="
                    background:white;
                    padding:20px;
                    border-radius:10px;
                "
            >
                <h2>Your cart is empty.</h2>
            </div>

        @endforelse

        <h2 style="margin-top:30px;">
            Total Price: ₹{{ $total }}
        </h2>

        <br><br>

        <button
            style="
                padding:12px;
                background:green;
                color:white;
                border:none;
                cursor:pointer;
            "
        >
            Proceed To Checkout
        </button>

    </div>

</body>
</html>