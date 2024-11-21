<div class="container">
    <div class="result-container shadow-lg p-4 mb-5 bg-white rounded">
        <h2 class="text-primary font-weight-bold mb-4">Resultados para:</h2>
        <p class="criteria-text mb-3">
            @if (count($criteriosBusqueda) > 0)
                @foreach ($criteriosBusqueda as $i => $criterio)
                    <span class="badge p-2 m-1 text-dark bg-info"
                        style="letter-spacing: 0.5px; text-transform:uppercase;">
                        {{ strtoupper($criterio) }}
                    </span>
                    @if (count($criteriosBusqueda) !== $i + 1)
                        <span><i class="fa fa-chevron-right"></i></span>
                    @endif
                @endforeach
            @else
                <span class="badge p-2 m-1 text-dark bg-info" style="letter-spacing: 0.5px;">
                    {{ $criterioBusqueda }}
                </span>
            @endif
        </p>
        <hr class="my-4">

        <div class="pagination-container mb-3">
            <div class="pagination-links">
                {{ $productos->links() }}
            </div>
            <div class="pagination-info">
                <span>Mostrando página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
            </div>
        </div>


        <div class="d-flex justify-content-end align-items-center mb-3">

            <div class="btn-group me-2">
                <button class="btn btn-light sort-price-btn {{ request('sort_price') == 'asc' ? 'active' : '' }}"
                    data-sort-price="asc" title="Ordenar por precio: menor a mayor">
                    <i class="bi bi-sort-numeric-down"></i>
                </button>
                <button class="btn btn-light sort-price-btn {{ request('sort_price') == 'desc' ? 'active' : '' }}"
                    data-sort-price="desc" title="Ordenar por precio: mayor a menor">
                    <i class="bi bi-sort-numeric-down-alt"></i>
                </button>
            </div>

            <div class="btn-group me-2">
                <button class="btn btn-light sort-name-btn {{ request('sort_name') == 'asc' ? 'active' : '' }}"
                    data-sort-name="asc" title="Ordenar alfabéticamente: A-Z">
                    <i class="bi bi-sort-alpha-down"></i>
                </button>
                <button class="btn btn-light sort-name-btn {{ request('sort_name') == 'desc' ? 'active' : '' }}"
                    data-sort-name="desc" title="Ordenar alfabéticamente: Z-A">
                    <i class="bi bi-sort-alpha-down-alt"></i>
                </button>
            </div>

            <div class="btn-group me-2">
                <button
                    class="btn {{ request('sort_price') || request('sort_name') || request('sort_offer') ? 'btn-warning' : 'btn-secondary' }}"
                    id="resetSortingBtn" title="Restablecer orden predeterminado">
                    <i class="bi bi-arrow-counterclockwise"></i> Restablecer Orden
                </button>
            </div>


            <div class="btn-group">
                <button id="gridViewBtn"
                    class="btn btn-light {{ session('preferredView') == 'grid' ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button id="listViewBtn"
                    class="btn btn-light {{ session('preferredView') == 'list' ? 'active' : '' }}">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>

        <div class="row">

            <div class="col-md-2">
                <div class="sidebars p-3 shadow-sm rounded bg-light">
                    <h4 class="mb-4">Filtros</h4>
                    @if (isset($division))
                        @if (isset($grupo) && isset($categoria))
                        <form action="{{ url('/categorias/' . $division . '/' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($grupo)) . '/' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($categoria))) }}" method="GET">

                                method="GET">
                                <input type="hidden" name="division" value="{{ $division }}">
                                <input type="hidden" name="grupo" value="{{ $grupo }}">
                                <input type="hidden" name="categoria" value="{{ $categoria }}">
                            @elseif (isset($grupo))
                                <form action="{{ url('/categorias/' . $division . '/' . $grupo) }}" method="GET">
                                    <input type="hidden" name="division" value="{{ $division }}">
                                    <input type="hidden" name="grupo" value="{{ $grupo }}">
                                @else
                                    <form action="{{ url('/categorias/' . $division) }}" method="GET">
                                        <input type="hidden" name="division" value="{{ $division }}">
                        @endif
                    @else
                        <form action="{{ route('product.search') }}" method="GET">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Filtro de Precio -->
                    <div class="filter-section mb-4">
                        <h5 class="mb-3">Precio</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span id="minPriceDisplay">Mínimo: $0 MXN</span>

                        </div>
                        <input type="range" class="form-range" min="0" max="300000" step="10"
                            id="minPriceRange" name="min_price" value="{{ request('min_price', 0) }}">
                        <div class="d-flex justify-content-between mb-2"><span id="maxPriceDisplay">Máximo: $300,000
                                MXN</span></div>
                        <input type="range" class="form-range mt-2" min="0" max="300000" step="100"
                            id="maxPriceRange" name="max_price" value="{{ request('max_price', 300000) }}">
                    </div>

                    <div class="text-center mb-4">
                        <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                    </div>
                    </form>

                    <!-- Filtros Aplicados -->
                    @if (request('min_price') || request('max_price'))
                        <div class="applied-filters">
                            <h5 class="mb-3">Filtros Aplicados</h5>
                            <ul class="list-unstyled">
                                @if (request('min_price') && request('max_price'))
                                    <li>Precio: Desde ${{ request('min_price') }} MXN hasta
                                        ${{ request('max_price', $maxPriceInDatabase) }} MXN
                                        <a href="{{ url()->current() . '?' . http_build_query(request()->except(['min_price', 'max_price'])) }}"
                                            class="text-danger">(Quitar)</a>
                                    </li>
                                @elseif (request('min_price'))
                                    <li>Precio: Desde ${{ request('min_price') }} MXN
                                        <a href="{{ url()->current() . '?' . http_build_query(request()->except('min_price')) }}"
                                            class="text-danger">(Quitar)</a>
                                    </li>
                                @elseif (request('max_price'))
                                    <li>Precio: Hasta ${{ request('max_price', $maxPriceInDatabase) }} MXN
                                        <a href="{{ url()->current() . '?' . http_build_query(request()->except('max_price')) }}"
                                            class="text-danger">(Quitar)</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-10">
                <div id="productViewContainer" class="animate__animated">
                    <div class="grid-view d-none">
                        <div class="row">
                            @foreach ($productos as $producto)
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        <a href="{{ url('/producto/' . $producto->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($producto->nombre))) }}">

                                            <img src="{{ $producto->imagen_principal }}" class="card-img-top"
                                                alt="{{ $producto->nombre }}"
                                                style="max-height: 180px; object-fit: cover;">
                                        </a>
                                        <div class="card-body d-flex flex-column">
                                            <a href="{{ url('/producto/' . $producto->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($producto->nombre))) }}">

                                                class="text-decoration-none text-dark">
                                                <h5 class="card-title text-truncate">{{ $producto->nombre }}</h5>
                                            </a>
                                            <p class="card-text product-description">
                                                {!! \Illuminate\Support\Str::limit(strip_tags($producto->descripcion), 100) !!}
                                            </p>
                                            <div class="mt-auto">
                                                @if (isset($producto->descuento) && $producto->descuento > 0)
                                                    <p class="precio-original">
                                                        <del>${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                                            MXN</del>
                                                    </p>
                                                    <p class="precio-con-descuento">
                                                        <strong>${{ number_format($producto->precio_con_descuento, 2, '.', ',') }}
                                                            MXN</strong>
                                                    </p>
                                                    <span class="badge bg-danger">¡Oferta!</span>
                                                @else
                                                    <p class="precio-normal">
                                                        <strong>${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                                            MXN</strong>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Vista Lista (visible inicialmente) -->
                    <div class="list-view">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($productos as $producto)
                                    <tr class="product-item"
                                    onclick="window.location='{{ url('/producto/' . $producto->id . '-' . preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower($producto->nombre))) }}';"
                                    style="cursor: pointer;">
                                
                                            <td class="image">
                                                <img src="{{ $producto->imagen_principal }}"
                                                    alt="{{ $producto->nombre }}"
                                                    style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                            </td>
                                            <td class="product">
                                                <strong>{{ $producto->nombre }}</strong><br>
                                                <p class="product-description">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($producto->descripcion), 150) }}
                                                </p>

                                            </td>
                                            
                                            <td class="price text-right">
                                                @if (isset($producto->descuento) && $producto->descuento > 0)
                                                    <span class="precio-original">
                                                        <del>${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                                            MXN</del>
                                                    </span><br>
                                                    <span
                                                        class="precio-con-descuento">${{ number_format($producto->precio_con_descuento, 2, '.', ',') }}
                                                        MXN</span><br>
                                                    <span class="badge bg-danger">¡Oferta!</span>
                                                @else
                                                    <span
                                                        class="precio-normal">${{ number_format($producto->precio_unitario_IVAinc, 2, '.', ',') }}
                                                        MXN</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pagination-container mt-4">
            <div class="pagination-links">
                {{ $productos->links() }}
            </div>
            <div class="pagination-info">
                <span>Mostrando página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const gridViewBtn = $('#gridViewBtn');
        const listViewBtn = $('#listViewBtn');
        const listView = $('.list-view');
        const gridView = $('.grid-view');
        const container = $('#productViewContainer');
        const minPriceRangeInput = $('#minPriceRange');
        const maxPriceRangeInput = $('#maxPriceRange');
        const minPriceDisplay = $('#minPriceDisplay');
        const maxPriceDisplay = $('#maxPriceDisplay');

        function updatePriceDisplay() {
            let minPrice = parseInt(minPriceRangeInput.val());
            let maxPrice = parseInt(maxPriceRangeInput.val());


            if (minPrice > maxPrice) {
                minPriceRangeInput.val(maxPrice);
                minPrice = maxPrice;
            }

            minPriceDisplay.text(`Mínimo: $${minPrice.toLocaleString()} MXN`);
            maxPriceDisplay.text(`Máximo: $${maxPrice.toLocaleString()} MXN`);
        }

        function adjustSliderSensitivity() {
            const minPrice = parseInt(minPriceRangeInput.val());
            const maxPrice = parseInt(maxPriceRangeInput.val());
            const stepMin = minPrice <= 5000 ? 50 : 1000;
            const stepMax = maxPrice <= 5000 ? 50 : 1000;
            minPriceRangeInput.attr('step', stepMin);
            maxPriceRangeInput.attr('step', stepMax);
            updatePriceDisplay();
        }

        minPriceRangeInput.on('input', adjustSliderSensitivity);
        maxPriceRangeInput.on('input', adjustSliderSensitivity);

        updatePriceDisplay();

        function applyView(view) {
            if (view === 'grid') {
                listView.addClass('d-none');
                gridView.removeClass('d-none');
                gridViewBtn.addClass('active');
                listViewBtn.removeClass('active');
            } else {
                gridView.addClass('d-none');
                listView.removeClass('d-none');
                listViewBtn.addClass('active');
                gridViewBtn.removeClass('active');
            }
        }

        const savedView = localStorage.getItem('preferredView');
        if (savedView) {
            applyView(savedView);
        } else {
            applyView('list');
        }


        gridViewBtn.on('click', function() {
            if (gridView.hasClass('d-none')) {
                container.addClass('animate__fadeOut');
                setTimeout(() => {
                    applyView('grid');
                    localStorage.setItem('preferredView',
                        'grid');
                    container.removeClass('animate__fadeOut').addClass('animate__fadeIn');
                }, 500);
            }
        });

        listViewBtn.on('click', function() {
            if (listView.hasClass('d-none')) {
                container.addClass('animate__fadeOut');
                setTimeout(() => {
                    applyView('list');
                    localStorage.setItem('preferredView',
                        'list');
                    container.removeClass('animate__fadeOut').addClass('animate__fadeIn');
                }, 500);
            }
        });


        $('.sort-price-btn').on('click', function() {
            var sortPrice = $(this).data('sort-price');
            var sortName = getUrlParameter('sort_name');
            var sortOffer = getUrlParameter('sort_offer');
            updateSorting(sortPrice, sortName, sortOffer);
        });


        $('.sort-name-btn').on('click', function() {
            var sortName = $(this).data('sort-name');
            var sortPrice = getUrlParameter('sort_price');
            var sortOffer = getUrlParameter('sort_offer');
            updateSorting(sortPrice, sortName, sortOffer);
        });


        $('.sort-offer-btn').on('click', function() {
            var sortOffer = $(this).data('sort-offer');
            var sortPrice = getUrlParameter('sort_price');
            var sortName = getUrlParameter('sort_name');
            updateSorting(sortPrice, sortName, sortOffer);
        });

        function updateSorting(sortPrice, sortName, sortOffer) {
            var url = new URL(window.location.href);
            var params = url.searchParams;

            if (sortPrice) {
                params.set('sort_price', sortPrice);
            } else {
                params.delete('sort_price');
            }

            if (sortName) {
                params.set('sort_name', sortName);
            } else {
                params.delete('sort_name');
            }

            if (sortOffer) {
                params.set('sort_offer', sortOffer);
            } else {
                params.delete('sort_offer');
            }

            window.location.href = url.toString();
        }


        $('#resetSortingBtn').on('click', function() {
            resetSortingFilters();
        });


        function resetSortingFilters() {
            var url = new URL(window.location.href);
            var params = url.searchParams;


            params.delete('sort_price');
            params.delete('sort_name');
            params.delete('sort_offer');

            window.location.href = url.toString();
        }


        function updateResetButtonAppearance() {
            var sortPrice = getUrlParameter('sort_price');
            var sortName = getUrlParameter('sort_name');
            var sortOffer = getUrlParameter('sort_offer');

            if (sortPrice || sortName || sortOffer) {
                $('#resetSortingBtn').removeClass('btn-secondary').addClass('btn-warning');
            } else {
                $('#resetSortingBtn').removeClass('btn-warning').addClass('btn-secondary');
            }
        }

        updateResetButtonAppearance();

        function getUrlParameter(name) {
            var url = new URL(window.location.href);
            return url.searchParams.get(name);
        }
    });
</script>






<style>
    /* Estilo para el precio original (tachado) */
    .precio-original {
        color: #ff0000;
        /* Rojo */
        font-size: 0.9em;
    }

    /* Estilo para el precio con descuento (en grande y resaltado) */
    .precio-con-descuento {
        color: #28a745;
        /* Verde */
        font-size: 1.2em;
        font-weight: bold;
    }

    /* Estilo para el precio normal (sin descuento) */
    .precio-normal {
        color: #333;
        font-size: 1.2em;
        font-weight: bold;
    }

    /* Badge para ofertas */
    .badge.bg-danger {
        background-color: #dc3545;
        font-size: 0.9em;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
    }

    /* Cambia el color de text-muted a rojo */
    .text-muted {
        color: #ff0000 !important;
        /* Rojo */
    }

    /* Estilos para las tarjetas de productos */
    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
    }

    .card-title {
        font-size: 1.1em;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 0.9em;
        color: #666;
        margin-bottom: 15px;
        flex-grow: 1;
    }

    .card-img-top {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        max-height: 180px;
        object-fit: cover;
    }

    .grid-view .col-md-3 {
        display: flex;
        justify-content: center;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .table td {
        vertical-align: middle;
    }

    .image img {
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .product strong {
        color: #005f7f;
    }

    .price {
        font-size: 1.2em;
    }

    /* Estilos para los botones de cambio de vista */
    #gridViewBtn.active i,
    #listViewBtn.active i {
        color: #005f7f;
        /* Color activo */
    }

    #gridViewBtn i,
    #listViewBtn i {
        font-size: 1.5em;
        color: #666;
        /* Color inactivo */
    }

    /* Animaciones */
    .animate__animated {
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }

    .animate__fadeIn {
        animation-name: fadeIn;
    }

    .animate__fadeOut {
        animation-name: fadeOut;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }


    .pagination-container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        /* Permite que los elementos se ajusten en una nueva línea si es necesario */
    }

    .pagination-info {
        font-size: 0.9em;
        color: #555;
        order: 1;
        /* Orden normal */
    }

    .pagination-links {
        display: flex;
        justify-content: center;
        align-items: center;
        order: 2;
        /* Orden normal */
        flex-wrap: wrap;
        /* Permite que los elementos se ajusten en una nueva línea si es necesario */
    }

    @media (max-width: 576px) {
        .pagination-container {
            flex-direction: column;
        }

        .pagination-info {
            order: 2;
            /* Mueve el texto abajo del paginador */
            margin-top: 10px;
            /* Añade un margen superior para separar el texto del paginador */
        }

        .pagination-links {
            order: 1;
            /* Mueve el paginador arriba del texto */
            margin-bottom: 5px;
            /* Añade un margen inferior para separar el paginador del texto */
        }
    }


    .filter-section .d-flex span {
        font-size: 0.9em;
        font-weight: bold;
    }

    .filter-section .d-flex {
        align-items: center;
        justify-content: space-between;
    }

    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .btn.active {
        background-color: #0d6efd;
        color: #fff;
    }


    /* Estilos para el botón de restablecer ordenamiento */
    #resetSortingBtn {
        display: flex;
        align-items: center;
        border-radius: 5px;
    }

    #resetSortingBtn .bi {
        margin-right: 5px;
    }

    /* Estilo cuando hay filtros activos */
    .btn-warning {
        background-color: #ffc107;
        color: #212529;
        border-color: #ffc107;
    }

    /* Estilo cuando no hay filtros activos */
    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
        border-color: #6c757d;
    }
</style>
