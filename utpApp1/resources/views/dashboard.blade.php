<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CRUD de Datos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Botón para mostrar el formulario -->
                <button style="background-color: blue;" class="text-white px-4 py-2 rounded mb-4" onclick="toggleModal()">
                    Agregar Nuevo Dato
                </button>

                <!-- Contenedor de la tabla -->
                <div id="datos-container">
                    <p>Cargando datos...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar un nuevo dato -->
    <div id="modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Agregar Nuevo Dato</h2>
            <form id="form-agregar" class="flex flex-col gap-4" enctype="multipart/form-data">
                <input type="text" id="add-description" placeholder="Descripción" class="border px-4 py-2 rounded">
                <input type="file" id="add-image" accept="image/*" class="border px-4 py-2 rounded">
                <div class="flex justify-end gap-2">
                    <button style="background-color: red;" type="button" class="bg-gray-500 text-white px-4 py-2 rounded" onclick="toggleModal()">Cancelar</button>
                    <button style="background-color: blue;" type="button" class="bg-green-500 text-white px-4 py-2 rounded" onclick="addDato()">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar un dato -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 style="background-color: blue;" class="text-xl font-semibold mb-4">Editar Dato</h2>
            <form id="form-editar" class="flex flex-col gap-4" enctype="multipart/form-data">
                <input type="hidden" id="edit-id">
                <input type="text" id="edit-description" placeholder="Descripción" class="border px-4 py-2 rounded">
                
                <!-- Vista previa de la imagen actual -->
                <div class="mb-4">
                    <label for="edit-image">Imagen Actual:</label>
                    <div class="mb-2">
                        <img id="edit-image-preview" src="" alt="Imagen Actual" class="w-32 h-32 object-cover rounded">
                    </div>
                    <input type="file" id="edit-image" accept="image/*" class="border px-4 py-2 rounded">
                </div>
                
                <div class="flex justify-end gap-2">
                    <button style="background-color: red;" type="button" class="bg-gray-500 text-white px-4 py-2 rounded" onclick="toggleEditModal()">Cancelar</button>
                    <button style="background-color: blue;" type="button" class="bg-blue-500 text-white px-4 py-2 rounded" onclick="saveChanges()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const apiUrl = 'http://127.0.0.1:8000/api/dato';
        const container = document.getElementById('datos-container');

        // Mostrar/ocultar el modal de agregar
        function toggleModal() {
            const modal = document.getElementById('modal');
            modal.classList.toggle('hidden');
        }

        // Mostrar/ocultar el modal de editar
        function toggleEditModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.toggle('hidden');
        }

        // Cargar datos desde la API
        async function fetchDatos() {
            try {
                const response = await fetch(apiUrl);
                const datos = await response.json();
                renderDatos(datos);
            } catch (error) {
                container.innerHTML = `<p class="text-red-500">Error al cargar datos: ${error.message}</p>`;
            }
        }

        // Renderizar la tabla de datos
        function renderDatos(datos) {
            if (datos.length === 0) {
                container.innerHTML = '<p class="text-gray-500">No hay datos disponibles.</p>';
                return;
            }

            const table = `
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2 bg-gray-100">ID</th>
                            <th class="border px-4 py-2 bg-gray-100">Imagen</th>
                            <th class="border px-4 py-2 bg-gray-100">Descripción</th>
                            <th class="border px-4 py-2 bg-gray-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${datos.map(dato => `
                            <tr>
                                <td class="border px-4 py-2">${dato.id}</td>
                                <td class="border px-4 py-2">
                                    <img src="http://127.0.0.1:8000/storage/${dato.img}" alt="Imagen" class="w-16 h-16 object-cover rounded">
                                </td>
                                <td class="border px-4 py-2">${dato.descripcion}</td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <button style="background-color: blue;" class="bg-green-500 text-white px-2 py-1 rounded" onclick="editDato(${dato.id})">Editar</button>
                                    <button style="background-color: red;" class="bg-red-500 text-white px-2 py-1 rounded" onclick="deleteDato(${dato.id})">Eliminar</button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            container.innerHTML = table;
        }

        // Agregar nuevo dato
        async function addDato() {
            const descripcion = document.getElementById('add-description').value;
            const img = document.getElementById('add-image').files[0];

            const formData = new FormData();
            formData.append('descripcion', descripcion);
            formData.append('img', img);

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData,
                });
                toggleModal();
                fetchDatos();
            } catch (error) {
                alert('Error al agregar el dato: ' + error.message);
            }
        }

// Editar dato
// Editar dato
async function editDato(id) {
    try {
        const response = await fetch(`${apiUrl}/${id}`);
        if (!response.ok) {
            throw new Error(`Error en la respuesta: ${response.statusText}`);
        }
        const dato = await response.json();

        // Asignar los valores del dato al formulario de edición
        document.getElementById('edit-id').value = dato.id;
        document.getElementById('edit-description').value = dato.descripcion;

        // Guardar valores originales para compararlos luego
        document.getElementById('edit-description').dataset.originalValue = dato.descripcion;

        // Mostrar la imagen en el modal de edición (si existe)
        const imageElement = document.getElementById('edit-image-preview');
        if (imageElement) {
            imageElement.src = `http://127.0.0.1:8000/storage/${dato.img}`;
            imageElement.dataset.originalSrc = dato.img;
        }

        toggleEditModal();
    } catch (error) {
        console.error('Error al obtener el dato para editar:', error);
        alert('Hubo un problema al obtener los datos para editar.');
    }
}

// Guardar cambios en la edición
async function saveChanges() {
    const id = document.getElementById('edit-id').value;
    const descripcionField = document.getElementById('edit-description');
    const descripcion = descripcionField.value;
    const imgField = document.getElementById('edit-image');
    const img = imgField.files[0];
    const imageElement = document.getElementById('edit-image-preview');

    const formData = new FormData();

    // Comparar y agregar solo los campos que han cambiado
    if (descripcion !== descripcionField.dataset.originalValue) {
        formData.append('descripcion', descripcion);
    }
    if (img || (imageElement && imageElement.dataset.originalSrc === '')) {
        formData.append('img', img);
    }

    // Solo enviar si hay datos en el FormData
    if (Array.from(formData.keys()).length === 0) {
        alert('No se detectaron cambios para guardar.');
        return;
    }

    try {
        // Cambiar el método a POST
        const response = await fetch(`${apiUrl}/${id}`, {
            method: 'POST', // Usamos POST en lugar de PUT
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`Error al guardar los cambios: ${response.statusText}`);
        }

        toggleEditModal();
        fetchDatos(); // Actualizar la lista de datos
    } catch (error) {
        alert('Error al guardar los cambios: ' + error.message);
    }
}

        // Eliminar dato
        async function deleteDato(id) {
            if (confirm('¿Estás seguro de eliminar este dato?')) {
                try {
                    await fetch(`${apiUrl}/${id}`, { method: 'DELETE' });
                    fetchDatos();
                } catch (error) {
                    alert('Error al eliminar el dato: ' + error.message);
                }
            }
        }

        // Cargar los datos inicialmente
        fetchDatos();
    </script>
</x-app-layout>
