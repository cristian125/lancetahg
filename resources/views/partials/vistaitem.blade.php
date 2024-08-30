<section class="py-5">
  <div class="container">
    <div class="row gx-5">
      <aside class="col-lg-6">
        <div id="main-image-container" class="border rounded-4 mb-3 d-flex justify-content-center align-items-center" style="width: 100%; height: 400px; position: relative;">
          <img id="main-image" src="{{ $imagenPrincipal }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
          <div class="zoom-controls">
            <button id="zoom-in">+</button>
            <button id="zoom-out">-</button>
          </div>
        </div>
        <div class="d-flex justify-content-center container">
          <p class="fst-italic">Las imágenes de los productos son ilustrativas y pueden variar respecto al artículo real.</p>
        </div>
        @if(count($imagenesMiniaturas) > 0)
        <div class="d-flex justify-content-center mb-3">
          @foreach($imagenesMiniaturas as $imagen)
            <div class="border mx-1 rounded-2 item-thumb" data-image="{{ $imagen }}" style="width: 60px; height: 60px; background-image: url('{{ $imagen }}'); background-size: cover; background-position: center; cursor: pointer;">
            </div>
          @endforeach
        </div>
        
        @endif
      </aside>
      <main class="col-lg-6">
        <div class="ps-lg-3">
          <h4 class="title text-dark">{{ $producto->no_s }} -{{ $producto->descripcion }}</h4>
          <div class="row">
            <p class="fw-bold">Descripcion: </p>
            <p>{{ $producto->descripcion_alias }}</p>
          </div>
          <div class="d-flex flex-row my-3">
            <span class="text-success ms-2">2 {{ strtolower($producto->unidad_medida_venta) }} en stock</span>
          </div>
          <div class="mb-3">
            <span class="h5" id="precio">${{ number_format($producto->precio_unitario,2,'.',',') }} MXN</span>
            <span class="text-muted" id="unidad_medida">/{{ $producto->unidad_medida_venta }}</span>
          </div>
          <div class="row">
            <dt class="col-3">Categoría:</dt>
            <dd class="col-9">{{ $producto->cod_categoria_producto }}</dd>
          </div>
          <hr />
          <div class="row mb-4">
            <div class="col-md-4 col-6 mb-3">
              <label class="mb-2 d-block">Cantidad</label>
              <div class="input-group mb-3" style="width: 170px;">
                <button id="btnremoveqty" class="btn btn-primary border border-secondary px-3" type="button">
                  <i class="bi bi-dash"></i>
                </button>
                <input id="qty" type="text" class="form-control text-center border border-secondary" value="1" step="1" min="1" />
                <button id="btnaddqty" class="btn btn-primary border border-secondary px-3" type="button">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
          </div>
          <form action="{{ env('APP_URL') }}/producto/{{ $id }}" method="GET">
            {{-- <a href="#" class="btn btn-warning shadow-0"> Comprar ahora </a> --}}
            <button id="add-to-cart" data-id="{{ $producto->id }}" class="btn btn-primary shadow-0">
              <i class="me-1 fa fa-shopping-basket"></i> Añadir al carrito 
            </button>
            <a href="/carrito" class="btn btn-danger shadow-0"><i class="bi bi-eye"></i> Ver carrito</a>

          </form>
          <hr />
          <div class="row mb-4">
            <div class="row">
              <div class="col-sm-2 align-middle pt-2">
                <i class="fa-solid fa-credit-card" style="font-size: 48px;"></i>
              </div>
              <div class="col-sm-10">
                <h5>Pague con tarjetas de débito o crédito</h5>
                <img src="{{ asset('storage/img/cards-product-page.jpg') }}" alt="Formas de Pago aceptadas" style="width:300px;" >
                <p>No aceptamos American Express</p>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-2 align-middle pt-2">
                <i class="fa-solid fa-truck" style="font-size: 48px;"></i>
              </div>
              <div class="col-sm-10">
                <h5>Envío a todo México</h5>
                <p>Entrega de 2 a 5 días. <span class="fst-italic fw-bold">* Aplican restricciones.</span></p>
                <a href="{{ route('envios') }}">Más sobre información sobre envío y entrega ></a>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-2 align-middle pt-2">
                <i class="fa-solid fa-store" style="font-size: 48px;"></i>
              </div>
              <div class="col-sm-10">
                <h5>Recoja en tienda</h5>
                <p>Compre en línea y pase a recoger a la tienda establecida en el mismo día. <span class="fst-italic fw-bold">* Aplican restricciones.</span></p>
                <a href="{{ route('envios') }}">Más sobre información sobre envío y entrega ></a>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</section>
