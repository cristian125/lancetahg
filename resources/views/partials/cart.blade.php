<div class="container mt-5" id="unique-cart-container">
    <h1 class="mb-4 text-center" id="unique-cart-title">Tu Carrito de Compras</h1>
    @if($cartItems->isEmpty())
        <div class="empty-cart text-center" id="unique-empty-cart">
            <img src="{{ asset('storage/icons/empty_cart.svg') }}" alt="Carrito vacío" class="img-fluid mb-4" style="max-width: 200px;">
            <p>No hay productos en tu carrito. <a href="{{ url('/') }}">Continúa comprando</a></p>
        </div>
    @else
        <div class="unique-cart-items" id="unique-cart-items">
            @foreach($cartItems as $item)
                <div class="unique-cart-item d-flex align-items-center mb-4 p-3 border-bottom shadow-sm">
                    <div class="item-image-wrapper">
                        <a href="{{ url('/producto/' . $item->id) }}">
                            <img src="{{ asset('storage/' . $item->image) }}" class="img-thumbnail" alt="{{ $item->name }}" style="width: 150px; height: 150px; object-fit: cover;">
                        </a>
                    </div>
                    <div class="item-details flex-grow-1 ms-4">
                        <a href="{{ url('/producto/' . $item->id) }}" class="text-decoration-none">
                            <h5 class="mb-2">{{ $item->name }}</h5>
                        </a>
                        @php
                            // dd($item);
                        @endphp
                        <p class="mb-2 text-muted">{{ $item->description }}</p>
                        <div class="d-flex align-items-center mb-2">
                            <p class="mb-0 me-4"><strong>Cantidad:</strong> {{ $item->quantity }} {{ $item->unidad }}</p>
                            <p class="mb-0 unique-product-price bg-light p-2 rounded"><strong>Precio:</strong> $ {{ number_format($item->price,2,'.',',') }} MXN</p>
                        </div>
                    </div>
                    <div class="unique-item-actions text-end">
                        <a href="#" class="btn btn-outline-danger btn-sm mb-2"><i class="bi bi-trash"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="unique-cart-summary p-4 bg-light rounded shadow-sm mt-4">
            <h4 class="text-end mb-4">Total: ${{ number_format($totalPrice,2,'.',',') }} MXN</h4>
            <div class="text-end">
                <a href="{{ url('/checkout') }}" class="btn btn-primary btn-lg"><i class="bi bi-credit-card"></i> Proceder al Pago</a>
            </div>
        </div>
    @endif
</div>
