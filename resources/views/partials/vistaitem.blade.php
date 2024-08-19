<section class="py-5">
  <div class="container">
    <div class="row gx-5">
      <aside class="col-lg-6">
        <div class="border rounded-4 mb-3 d-flex justify-content-center">
          <!-- Mostrar la imagen principal -->
          <a id="main-image-link" class="rounded-4" target="_blank" href="{{ $imagenPrincipal }}">
            <img id="main-image" style="max-width: 100%; max-height: 100vh; margin: auto;" class="rounded-4 fit" src="{{ $imagenPrincipal }}" />
          </a>
        </div>
        <div class="d-flex justify-content-center mb-3">
          @foreach($imagenesMiniaturas as $index => $imagen)
            <a data-fslightbox="mygalley" class="border mx-1 rounded-2 item-thumb" target="_blank" href="{{ $imagen }}"
               data-precio="{{ $variantes[$index]->precio ?? $producto->precio_final }}"
               data-imagen="{{ $imagen }}">
              <img width="60" height="60" class="rounded-2" src="{{ $imagen }}" />
            </a>
          @endforeach
        </div>
      </aside>
      <main class="col-lg-6">
        <div class="ps-lg-3">
          <h4 class="title text-dark">{{ $producto->nombre }}</h4>
          <div class="d-flex flex-row my-3">
            <span class="text-success ms-2">En stock</span>
          </div>
          <div class="mb-3">
            <span class="h5" id="precio">{{ $producto->precio_final }} MXN</span>
            <span class="text-muted" id="unidad_medida">/{{ $producto->unidad_medida }}</span>
          </div>
          <p>{{ $producto->descripcion }}</p>
          <div class="row">
            <dt class="col-3">Marca:</dt>
            <dd class="col-9">{{ $producto->marca }}</dd>
            @foreach($variantes->groupBy('atributo') as $atributo => $valores)
            <dt class="col-3">{{ ucfirst($atributo) }}:</dt>
            <dd class="col-9">
            <select class="form-select border border-secondary variante-select" data-atributo="{{ $atributo }}">
              @foreach($valores as $variante)
                  <option value="{{ $variante->valor }}"
                          data-precio="{{ $variante->precio }}"
                          data-unidad_medida="{{ $producto->unidad_medida }}"
                          data-descripcion="{{ $producto->descripcion }}"
                          data-marca="{{ $producto->marca }}"
                          data-imagen="{{ $variante->image_path ? asset('storage/' . $variante->image_path) : asset('storage/itemsview/default.jpg') }}">
                      {{ $variante->valor }}
                  </option>
              @endforeach
          </select>
            </dd>
            @endforeach
          </div>
          <hr />
          <div class="row mb-4">
            <div class="col-md-4 col-6 mb-3">
              <label class="mb-2 d-block">Cantidad</label>
              <div class="input-group mb-3" style="width: 170px;">
                <button class="btn btn-white border border-secondary px-3" type="button">
                  <i class="fas fa-minus"></i>
                </button>
                <input type="text" class="form-control text-center border border-secondary" value="1" />
                <button class="btn btn-white border border-secondary px-3" type="button">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
          </div>
          <a href="#" class="btn btn-warning shadow-0"> Comprar ahora </a>
          <a href="#" class="btn btn-primary shadow-0"> <i class="me-1 fa fa-shopping-basket"></i> Añadir al carrito </a>
          <a href="#" class="btn btn-light border border-secondary py-2 icon-hover px-3"> <i class="me-1 fa fa-heart fa-lg"></i> Favorito </a>
        </div>
      </main>
    </div>
  </div>
</section>
