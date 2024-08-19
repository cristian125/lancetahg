<div id="contenedorsup" class="container d-flex justify-content-center">
  <div class="row">
<!-- LOS CUATRO CONTENEDORES DE 2X2 CON LAS OFERTAS --> 
    <div class="col-md-5 order-md-1">
      <div class="row mt-4">
        <div class="col-md-6">
          <div class="p-2">
            <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 1" class="img-fluid">
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-2">
            <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 2" class="img-fluid">
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-6">
          <div class="p-2">
            <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 3" class="img-fluid">
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-2">
            <img src="{{ asset('storage/carousel/pruebacuadricula.jpg') }}" alt="Imagen 4" class="img-fluid">
          </div>
        </div>
      </div>
    </div>

<!-- CARROUSEL --> 
    <div class="col-md-7 order-md-2 align-items-center">
      <div id="carouselExample" class="carousel slide carousel-custom-size" data-bs-ride="carousel" data-bs-interval="3000">
        <!-- Indicadores -->
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="{{ asset('storage/carousel/loreip.jpg') }}" class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item">
            <img src="{{ asset('storage/carousel/loreip.jpg') }}" class="d-block w-100" alt="...">
          </div>
          <div class="carousel-item">
            <img src="{{ asset('storage/carousel/test1.jpg') }}" class="d-block w-100" alt="...">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- CONTENEDOR QUE DIVIDE LAS SECCIONES --> 
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-4 text-center" id="item-centrado">
            Productos Destacados
        </div>
    </div>
</div>

<!-- CREACION DE LOS ITEMS Y LOS CUADROS DE ITEMS EN LA PAGINA PRINCIPAL --> 
<div class="container mt-4">
    @foreach($destacados->chunk(4) as $chunk)
        <div class="row mt-4">
            @foreach($chunk as $producto)
                <div class="col-md-3">
                    <a href="{{ url('/producto/' . $producto->id) }}" class="text-decoration-none">
                        <div class="product-container">
                            <img src="{{ asset('storage/itemsview/' . $producto->codigo . '.jpg') }}" alt="{{ $producto->nombre }}" class="img-fluid">
                            <div class="overlay">
                                <div class="overlay-text">Ver</div>
                            </div>
                            <div class="product-info">
                                <p class="text-dark">{{ $producto->nombre }}</p>
                                <p class="text-muted">{{ $producto->marca }}</p>
                                <p class="text-dark">{{ $producto->precio_final }} MXN</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

