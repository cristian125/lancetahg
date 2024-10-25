@extends('admin.template')  

@section('header')
    <script src="https://cdn.tiny.cloud/1/wvaefm1arxme7m88dltt36jyacb4oqanhvybh3pu7972u0ok/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
@endsection


@section('content')

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-start align-items-center mb-4">
        <!-- Botón para regresar alineado a la izquierda -->
        <a href="{{ route('admin.pages.list') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>

    <div class="card shadow-sm p-4">
        <h2 class="text-center mb-4">Editando: {{ $page->title }}</h2>

        <!-- Mostrar mensaje de éxito si existe -->
        @if (session('success'))
            <div id="success-message" class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mostrar errores de validación si existen -->
        @if ($errors->any())
            <div id="error-message" class="alert alert-danger">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario para editar la página -->
        <form id="editForm" action="{{ route('admin.editor.save', $page->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Campo de Título -->
            <div class="form-group mb-3">
                <label for="title" class="form-label fw-bold">Título</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ $page->title }}" required>
            </div>

            <!-- Campo de Slug -->
            <div class="form-group mb-3">
                <label for="slug" class="form-label fw-bold">Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" value="{{ $page->slug }}" required>
                <small class="form-text text-muted">El slug es la parte de la URL que identifica a la página. Debe ser único y sin espacios.</small>
            </div>

            <!-- Editor de TinyMCE para el contenido -->
            <div id="editor-container" class="form-group mb-5">
                <label for="content" class="form-label fw-bold">Contenido</label>
                <textarea id="editor" name="content" class="form-control">{{ $page->content }}</textarea>
            </div>

            <!-- Botón de enviar (dispara el modal) -->
            <div class="d-flex justify-content-center mt-4">
                <button type="button" class="btn btn-primary btn-lg" id="showModalBtn">Guardar Contenido</button>
            </div>
        </form>

        <!-- Botón para eliminar la página -->
        <div class="d-flex justify-content-center mt-4">
            <button type="button" class="btn btn-danger btn-lg" id="showDeleteModalBtn">Eliminar Página</button>
        </div>

    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que quieres eliminar esta página? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <!-- Formulario para eliminar la página -->
                <form id="deleteForm" action="{{ route('admin.pages.delete', $page->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para guardar cambios -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <!-- ... código existente del modal ... -->
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- ... contenido del modal ... -->
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Guardado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que quieres guardar los cambios?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmSaveBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    tinymce.init({
        selector: '#editor', 
        language: 'es',
        height: 800,
        // plugins: [
        //     'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media',
        //     'searchreplace', 'table', 'visualblocks', 'wordcount', 'checklist', 'mediaembed', 'casechange',
        //     'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen',
        //     'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'mentions', 'tableofcontents',
        //     'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown',
        // ],
        spellchecker_language: 'es',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        setup: function(editor) {
            editor.on('init', function() {
                const scripts = [
                    { src: '{{ asset("js/jquery/jquery-3.7.1.min.js") }}' },
                    { src: '{{ asset("js/bootstrap/bootstrap.min.js") }}' }
                ];

                const styles = [
                    { href: 'http://lanceta.com:82/css/{{ $page->slug }}.css' },
                    { href: '{{ asset("css/app.css") }}' }
                ];

                scripts.forEach((script) => {
                    const sc = document.createElement('script');
                    sc.src = script.src;
                    editor.getDoc().head.appendChild(sc);
                });

                styles.forEach((style) => {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = style.href;
                    editor.getDoc().head.appendChild(link);
                });
            });
        },
    });

    // Mostrar el modal de confirmación de guardado
    document.getElementById('showModalBtn').addEventListener('click', function() {
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    });

    document.getElementById('confirmSaveBtn').addEventListener('click', function() {
        document.getElementById('editForm').submit();
    });

    // Mostrar el modal de confirmación de eliminación
    document.getElementById('showDeleteModalBtn').addEventListener('click', function() {
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    });

    // Ocultar mensajes después de 4 segundos
    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 4000);
        }

        var errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 4000);
        }

        // Generación automática del slug
        document.getElementById('title').addEventListener('input', function() {
            var slugInput = document.getElementById('slug');
            var title = this.value;

            // Generar el slug reemplazando espacios por guiones y convirtiendo a minúsculas
            var slug = title.toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '')   // Eliminar caracteres especiales
                .replace(/\s+/g, '-')           // Reemplazar espacios por guiones
                .replace(/-+/g, '-');           // Reemplazar múltiples guiones por uno solo

            slugInput.value = slug;
        });
    });
</script>

<style>
    body {
        background: rgb(195,195,195);
        background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
    }
    .tox-notification{
        display: none !important;
    }
</style>
@endsection
