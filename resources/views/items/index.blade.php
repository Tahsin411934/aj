<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Table with AJAX</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Items Table</h1>

        <!-- Add Item Button -->
        <button class="bg-blue-500 text-white px-4 py-2 rounded mb-4" id="openModal">
            Add Item
        </button>

        <!-- Items Table -->
        <table class="min-w-full bg-white border rounded shadow" id="itemsTable">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr id="item-{{ $item->id }}">
                    <td class="px-4 py-2 border">{{ $item->id }}</td>
                    <td class="px-4 py-2 border">{{ $item->name }}</td>
                    <td class="px-4 py-2 border">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 border">{{ $item->price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Item Modal -->
    <div id="modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Add New Item</h2>
            <form id="addItemForm">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block font-medium">Name</label>
                    <input type="text" id="name" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block font-medium">Quantity</label>
                    <input type="number" id="quantity" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="price" class="block font-medium">Price</label>
                    <input type="number" id="price" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded mr-2" id="closeModal">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script -->
    <script>
        $(document).ready(() => {
            const modal = $('#modal');
            const form = $('#addItemForm');

            const toggleModal = (show) => {
                show ? modal.removeClass('hidden') : modal.addClass('hidden');
            };

            const appendItem = (item) => {
                $('#itemsTable tbody').append(`
                    <tr id="item-${item.id}">
                        <td class="px-4 py-2 border">${item.id}</td>
                        <td class="px-4 py-2 border">${item.name}</td>
                        <td class="px-4 py-2 border">${item.quantity}</td>
                        <td class="px-4 py-2 border">${item.price}</td>
                    </tr>
                `);
            };

            // Open and Close Modal
            $('#openModal').click(() => toggleModal(true));
            $('#closeModal').click(() => toggleModal(false));

            // Submit Form with AJAX
            form.on('submit', (e) => {
                e.preventDefault();
                $.post('{{ route('items.store') }}', {
                    name: $('#name').val(),
                    quantity: $('#quantity').val(),
                    price: $('#price').val(),
                    _token: '{{ csrf_token() }}'
                })
                .done((response) => {
                    if (response.success) {
                        appendItem(response.item);
                        toggleModal(false);
                        form[0].reset();
                        alert('Item added successfully!');
                    }
                })
                .fail((xhr) => {
                    console.error(xhr.responseText);
                    alert('An error occurred while adding the item.');
                });
            });
        });
    </script>

</body>
</html>
