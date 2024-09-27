<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Artículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
        <div>
            <h1>Lista de Artículos</h1>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Nombre</th>
                    </tr>
                </thead>
                <tbody id="articulos-tbody"></tbody>
            </table>

            <nav class="d-flex justify-content-center" aria-label="Page navigation">
                <ul class="pagination" id="pagination"></ul>
            </nav>
        </div>

        <hr>

        <div>
            <h1 class="my-3">Crear/Editar Artículo</h1>
            <form id="form-crear-articulo" class="mt-3">
                <input type="hidden" name="id" id="articulo-id">
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código del Artículo</label>
                    <input type="text" class="form-control" name="codigo" placeholder="Código" required maxlength="10">
                    <h5 class="form-text">El código debe ser solo texto y tener un máximo de 10 caracteres.</h5>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" name="nombre" placeholder="Nombre" required>
                </div>
                <button type="submit" class="btn btn-primary">Crear</button>
            </form>
        </div>
</div>

<script>
    let currentPage = 1;

    async function cargarArticulos(page = 1) {
        try {
            const response = await fetch(`http://localhost/codeIgniter/articulos/read/${page}`);
            if (!response.ok) {
                throw new Error('Error al cargar los datos');
            }
            const { articulos, total_pages } = await response.json();

            const tbody = document.getElementById('articulos-tbody');
            tbody.innerHTML = ''; 
            articulos.forEach(articulo => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <th scope="row">${articulo.codigo}</th>
                    <td>${articulo.nombre}</td>
                `;
                row.addEventListener('click', () => {
                    cargarArticuloEnFormulario(articulo);
                });
                tbody.appendChild(row);
            });

            // Actualiza la paginacionn
            actualizarPaginacion(total_pages);
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al cargar los artículos.');
        }
    }

    function cargarArticuloEnFormulario(articulo) {
        document.getElementById('articulo-id').value = articulo.id;
        document.querySelector('input[name="codigo"]').value = articulo.codigo;
        document.querySelector('input[name="nombre"]').value = articulo.nombre;

        document.querySelector('button[type="submit"]').textContent = 'Actualizar';
    }

    function actualizarPaginacion(totalPages) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = i;
                cargarArticulos(currentPage);
            });
            pagination.appendChild(li);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        cargarArticulos(); // Cargar articulos al inicio

        const formCrearArticulo = document.getElementById('form-crear-articulo');
        formCrearArticulo.addEventListener('submit', async function(e) {
            e.preventDefault();

            const id = this.id.value; // Obtener el ID del articulo si existe
            const codigo = this.codigo.value;
            const nombre = this.nombre.value;

            if (!/^[a-zA-Z]+$/.test(codigo) || codigo.length > 10) {
                 alert('El código debe ser solo texto y tener un máximo de 10 caracteres.');
                return;
            }

            if (!/^[a-zA-Z]+$/.test(nombre)) {
                 alert('El código debe ser solo texto');
                return;
            }

            if (!nombre.trim()) {
                alert('El nombre es obligatorio.');
                return;
            }

            const formData = new FormData(this);

            try {
                const response = await fetch(id ? 'http://localhost/codeIgniter/articulos/update' : 'http://localhost/codeIgniter/articulos/create', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    alert(`Artículo ${id ? 'actualizado' : 'creado'} con éxito!`);
                    this.reset(); 
                    cargarArticulos(currentPage); // Actualiza la lista de articulos
                    document.querySelector('button[type="submit"]').textContent = 'Crear'; // Restablecer el texto del boton
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error al enviar la solicitud:', error);
                alert('Error al enviar la solicitud: ' + error);
            }
        });
    });
</script>
</body>
</html>