<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wawibox</title>

    <link rel="stylesheet" href="{{ url('assets') }}/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url('assets') }}/toastr.min.css">
    <link href="{{url('dataTableBootstrap5')}}/DataTables/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('dataTableBootstrap5')}}/DataTables/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        table.dataTable tbody td:nth-child(1){
            font-weight: 600;
        }
        table.dataTable tbody td{
            text-align: center !important;
        }
        table#DataTables_Table_0 img{
            transition: all .2s linear;
        }
        img.gridProductImage:hover{
            scale: 2;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6 border-end">
                    <h3 class="mt-3">Dental Product Price List</h3>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered table-hover yajra-datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Supplier Name</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Pack Size</th>
                                    <th class="text-center">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="col-lg-6 p-5">
                    <h3 class="mt-3">Find the best Supplier with best price</h3>
                    <table class="table mb-2 table-sm table-striped table-hover" id="orderTable">
                        <thead>
                            <tr>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Quantity (Peices)</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <select name="product_id[]" class="form-select" onchange="checkDuplicate(this)">
                                        <option value="">Select One</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="text" name="quantity[]" class="form-control" placeholder="1">
                                </td>
                                <td class="text-center"></td>
                            </tr>
                        </tbody>
                    </table>

                    <button class="btn d-block btn-sm btn-info rounded text-white addMoreRow" onclick="addMoreProduct()">Order More Product</button>
                    <button class="btn d-block mt-4 btn-success rounded text-white m-auto" onclick="checkInputValidation()">Compare Price & Find Best Supplier</button>


                    <div class="card mt-4" id="result_wrapper">
                        <div class="card-header">
                            Result
                        </div>
                        <div class="card-body">
                            <p>Best Supplier: <span id="best_supplier_name"></span></p>
                            <p>Best Price: <span id="best_price"></span></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script src="{{ url('assets') }}/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets') }}/jquery-3.7.1.min.js"></script>
    <script src="{{url('dataTableBootstrap5')}}/DataTables/js/jquery.dataTables.min.js"></script>
    <script src="{{url('dataTableBootstrap5')}}/DataTables/js/dataTables.bootstrap.min.js"></script>

    <script>

        var table = $('.yajra-datatable').DataTable({
            processing: true,
            stateSave: true,
            serverSide: true,
            pageLength: 10,
            lengthMenu: [10, 20, 50, 100],
            ajax: "{{ url('/') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'supplier', name: 'supplier'},
                {data: 'product', name: 'product'},
                {data: 'size', name: 'size'},
                {data: 'price', name: 'price'},
            ]
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function addMoreProduct() {
            $(".addMoreRow").html("Adding...");
            $.ajax({
                data: '',
                url: "{{ url('/add/another/product') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $(".addMoreRow").html("Added");
                    $("#orderTable tbody").append(data.variant);
                    $(".addMoreRow").html("Order More Product");
                },
                error: function(data) {
                    console.log('Error:', data);
                    $(".addMoreRow").html("Something Went Wrong");
                }
            });
            $(".addMoreRow").blur();
        }

        function removeRow(btndel) {
            if (typeof(btndel) == "object") {
                $(btndel).closest("tr").remove();
            } else {
                return false;
            }
        }

        function checkDuplicate(element) {
            let duplicateProductFields = document.getElementsByName("product_id[]");
            for (let i = 0; i < duplicateProductFields.length; i++) {
                if (duplicateProductFields[i] !== element && duplicateProductFields[i].value === element.value) {
                    toastr.error("Product Already Selected");
                    element.value = "";
                    return false;
                }
            }
        }

        function checkInputValidation() {

            // Input Validation started
            let productFields = document.getElementsByName("product_id[]");
            for (let i = 0; i < productFields.length; i++) {
                if(!productFields[i].value){
                    productSerial = i+1;
                    toastr.error("Product-"+productSerial+" is Missing");
                    return false;
                }
            }

            let productQtyFields = document.getElementsByName("quantity[]");
            for (let i = 0; i < productQtyFields.length; i++) {
                let qtyValue = productQtyFields[i].value;
                if (!qtyValue || isNaN(qtyValue) || qtyValue <= 0) {
                    let productSerial = i + 1;
                    toastr.error("Invalid Product-" + productSerial + " Quantity");
                    return false;
                }
            }

            comparePrice();
        }

        function comparePrice(){

            let product_id_array = [];
            let product_qty_array = [];

            $("select[name='product_id[]']").each(function() {
                product_id_array.push($(this).val());
            });

            $("input[name='quantity[]']").each(function() {
                product_qty_array.push($(this).val());
            });

            var formData = new FormData();
            formData.append("product_ids", String(product_id_array));
            formData.append("product_qtys", String(product_qty_array));

            $.ajax({
                data: formData,
                url: "{{ url('compare/suppliers/price') }}",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#result_wrapper').fadeOut(function() {
                        $(this).fadeIn();
                        $("#best_supplier_name").text(data.best_supplier);
                        $("#best_price").text(data.total_cost+" EUR");
                        toastr.success("Price Compared Successfully");
                    });
                },
                error: function(data) {
                    toastr.error("Something Went Wrong");
                }
            });

        }
    </script>

    <script src="{{ url('assets') }}/toastr.min.js"></script>
    {!! Toastr::message() !!}
</body>

</html>
