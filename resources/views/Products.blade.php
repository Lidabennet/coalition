<!DOCTYPE html>
<html>
<head>
    <title>Product Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="container mt-5">

<h3>Product Manager</h3>

<form id="productForm" class="row g-3 mb-4">
    <input type="hidden" id="edit_id">

    <div class="col-md-4">
        <input type="text" id="name" class="form-control" placeholder="Product name" required>
    </div>

    <div class="col-md-3">
        <input type="number" id="quantity" class="form-control" placeholder="Quantity in stock" required>
    </div>

    <div class="col-md-3">
        <input type="number" step="0.01" id="price" class="form-control" placeholder="Price per item" required>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100" type="submit">Submit</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product name</th>
            <th>Quantity in stock</th>
            <th>Price per item</th>
            <th>Datetime submitted</th>
            <th>Total value number</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="tableBody"></tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total sum</th>
            <th id="grandTotal"></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<script>
$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

function loadProducts() {
    $.get('/products', function(data) {
        let rows = '';
        let sum = 0;

        data.forEach(item => {
            sum += item.total;

            rows += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price}</td>
                    <td>${item.datetime}</td>
                    <td>${item.total}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editItem(${JSON.stringify(item)})'>Edit</button>
                    </td>
                </tr>`;
        });

        $('#tableBody').html(rows);
        $('#grandTotal').text(sum.toFixed(2));
    });
}
$('#productForm').submit(function(e) {
    e.preventDefault();

    console.log('Form is submitted');

    let id = $('#edit_id').val();
    let url = id ? `/products/${id}` : '/products';

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            name: $('#name').val(),
            quantity: $('#quantity').val(),
            price: $('#price').val()
        },
        success: function() {
            $('#productForm')[0].reset();
            $('#edit_id').val('');
            loadProducts();
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('Server error – check laravel.log');
        }
    });
});



function editItem(item) {
    $('#edit_id').val(item.id);
    $('#name').val(item.name);
    $('#quantity').val(item.quantity);
    $('#price').val(item.price);
}

loadProducts();
</script>

</body>
</html>
