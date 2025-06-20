<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <div class="container my-4">
        <h2>{{ $product->name }}</h2>
        <p>{{ $product->description }}</p>
        {{-- @dd($product->variants[0]->values) --}}
        <form>
            <div class="row" id="variant-selectors">
                @foreach ($attributes as $attribute)
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ $attribute->name }}</label>
                        <select class="form-select variant-select" data-attribute-id="{{ $attribute->id }}">
                            <option value="">Select {{ $attribute->name }}</option>
                            @foreach ($attribute->values as $value)
                                <option value="{{ $value->id }}">{{ $value->value }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        </form>
        <form id="cart-form">
            <input type="hidden" name="variant_id" id="selected-variant-id">
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="1" class="form-control" min="1"
                    required>
            </div>
            <button type="submit" class="btn btn-success">Add to Cart</button>
        </form>
        <div id="cart-message" class="mt-3 text-success fw-bold"></div>
        <div class="mt-4">
            <h5>Price: <span id="variant-price">-</span></h5>
            <h5>Stock: <span id="variant-stock">-</span></h5>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- ... your product dropdowns and cart form here ... --}}

    {{-- <form id="cart-form">
        <input type="hidden" name="variant_id" id="selected-variant-id">
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" value="1" class="form-control" min="1"
                required>
        </div>
        <button type="submit" class="btn btn-success">Add to Cart</button>
    </form>

    <div id="cart-message" class="mt-3 text-success fw-bold"></div> --}}

    {{-- 👇 Script below all HTML --}}
    <script>
        const variantMap = @json($variantMap);
        const priceDisplay = $('#variant-price');
        const stockDisplay = $('#variant-stock');
        let currentVariantId = null;

        function filterDropdowns(changedSelect) {
            const selected = {};

            $('.variant-select').each(function() {
                const attrId = $(this).data('attribute-id');
                const val = $(this).val();
                if (val) selected[attrId] = parseInt(val);
            });

            $('.variant-select').each(function() {
                const attrId = $(this).data('attribute-id');
                const originalValue = $(this).val();

                if (!$(changedSelect).val()) {
                    $(this).find('option').prop('disabled', false);
                    priceDisplay.text('-');
                    stockDisplay.text('-');
                    return;
                }

                if (parseInt($(changedSelect).data('attribute-id')) !== parseInt(attrId)) {
                    const validValues = new Set();

                    variantMap.forEach(function(combo) {
                        let match = true;
                        for (const key in selected) {
                            if (parseInt(key) === parseInt(attrId)) continue;
                            if (combo[key] !== selected[key]) {
                                match = false;
                                break;
                            }
                        }
                        if (match && combo[attrId]) {
                            validValues.add(combo[attrId]);
                        }
                    });

                    $(this).find('option').each(function() {
                        const optionVal = $(this).val();
                        if (!optionVal || validValues.has(parseInt(optionVal))) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });

                    if (originalValue && !validValues.has(parseInt(originalValue))) {
                        $(this).val('');
                    }
                }
            });

            // AJAX for price & stock + variant_id for cart
            if (Object.keys(selected).length === $('.variant-select').length) {
                $.ajax({
                    url: "{{ route('products.getVariant') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({
                        product_id: {{ $product->id }},
                        attribute_value_ids: Object.values(selected)
                    }),
                    success: function(data) {
                        if (data.price) {
                            priceDisplay.text(`$${data.price}`);
                            stockDisplay.text(data.stock);
                        } else {
                            priceDisplay.text('-');
                            stockDisplay.text('-');
                        }

                        if (data.variant_id) {
                            $('#selected-variant-id').val(data.variant_id);
                            currentVariantId = data.variant_id;
                        }
                    }
                });
            } else {
                priceDisplay.text('-');
                stockDisplay.text('-');
                $('#selected-variant-id').val('');
                currentVariantId = null;
            }
        }

        $('.variant-select').on('change', function() {
            filterDropdowns(this);
        });

        $('#cart-form').on('submit', function(e) {
            e.preventDefault();
            const variantId = $('#selected-variant-id').val();
            const quantity = $('#quantity').val();

            if (!variantId) {
                alert('Please select valid variant first.');
                return;
            }

            $.ajax({
                url: "{{ route('cart.add') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    variant_id: variantId,
                    quantity: quantity
                },
                success: function(response) {
                    $('#cart-message').text(response.message);
                }
            });
        });
    </script>






    {{-- without add to cart script --}}
    {{-- <script>
        const variantMap = @json($variantMap);
        const priceDisplay = $('#variant-price');
        const stockDisplay = $('#variant-stock');

        function filterDropdowns(changedSelect) {
            const selected = {};
            // console.log('Filtering dropdowns based on:', changedSelect);

            $('.variant-select').each(function() {
                const attrId = $(this).data('attribute-id');
                const val = $(this).val();
                if (val) selected[attrId] = parseInt(val);

            });
            $('.variant-select').each(function() {
                const attrId = $(this).data('attribute-id');
                const originalValue = $(this).val();

                if (!$(changedSelect).val()) {
                    $(this).find('option').prop('disabled', false);
                    priceDisplay.text('-');
                    stockDisplay.text('-');
                    return;
                }

                if (parseInt($(changedSelect).data('attribute-id')) != parseInt(attrId)) {
                    const validValues = new Set();

                    variantMap.forEach(function(combo) {
                        let match = true;
                        for (const key in selected) {
                            if (parseInt(key) == parseInt(attrId)) continue;
                            if (combo[key] != selected[key]) {
                                match = false;
                                break;
                            }
                        }
                        if (match && combo[attrId]) {
                            validValues.add(combo[attrId]);
                        }
                    });
                    console.log(validValues);

                    $(this).find('option').each(function() {
                        const optionVal = $(this).val();
                        if (!optionVal || validValues.has(parseInt(optionVal))) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });

                    if (originalValue && !validValues.has(parseInt(originalValue))) {
                        $(this).val('');
                    }
                }
            });

            // AJAX for price & stock
            if (Object.keys(selected).length == $('.variant-select').length) {
                $.ajax({
                    url: "{{ route('products.getVariant') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({
                        product_id: {{ $product->id }},
                        attribute_value_ids: Object.values(selected)
                    }),
                    success: function(data) {
                        if (data.price) {
                            priceDisplay.text(`$${data.price}`);
                            stockDisplay.text(data.stock);
                        } else {
                            priceDisplay.text('-');
                            stockDisplay.text('-');
                        }
                    },
                    error: function() {
                        priceDisplay.text('-');
                        stockDisplay.text('-');
                    }
                });
            } else {
                priceDisplay.text('-');
                stockDisplay.text('-');
            }
        }

        $('.variant-select').on('change', function() {
            filterDropdowns(this);
        });
    </script> --}}



    {{-- javascript me --}}
    {{-- <script>
        const variantMap = @json($variantMap);
        const selects = document.querySelectorAll('.variant-select');
        const priceDisplay = document.getElementById('variant-price');
        const stockDisplay = document.getElementById('variant-stock');

        function filterDropdowns(changedSelect) {
            const selected = {};

            selects.forEach(select => {
                const attrId = select.dataset.attributeId;
                const val = select.value;
                if (val) selected[attrId] = parseInt(val);
            });

            // Loop each select and enable/disable options
            selects.forEach(select => {
                const attrId = select.dataset.attributeId;
                const originalValue = select.value;

                // If current select was cleared, reset all others
                if (!changedSelect.value) {
                    select.querySelectorAll('option').forEach(option => {
                        option.disabled = false;
                    });
                    priceDisplay.innerText = '-';
                    stockDisplay.innerText = '-';
                    return;
                }

                // Otherwise, filter other dropdowns
                if (parseInt(changedSelect.dataset.attributeId) !== parseInt(attrId)) {
                    const validValues = new Set();

                    variantMap.forEach(combo => {
                        let match = true;
                        for (const key in selected) {
                            if (parseInt(key) === parseInt(attrId)) continue;
                            if (combo[key] !== selected[key]) {
                                match = false;
                                break;
                            }
                        }
                        if (match && combo[attrId]) {
                            validValues.add(combo[attrId]);
                        }
                    });

                    select.querySelectorAll('option').forEach(option => {
                        if (!option.value || validValues.has(parseInt(option.value))) {
                            option.disabled = false;
                        } else {
                            option.disabled = true;
                        }
                    });

                    // Deselect invalid value if needed
                    if (originalValue && !validValues.has(parseInt(originalValue))) {
                        select.value = '';
                    }
                }
            });

            // Now match full selected combo to show price/stock
            if (Object.keys(selected).length === selects.length) {
                fetch("{{ route('products.getVariant') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: {{ $product->id }},
                            attribute_value_ids: Object.values(selected)
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.price) {
                            priceDisplay.innerText = `$${data.price}`;
                            stockDisplay.innerText = data.stock;
                        } else {
                            priceDisplay.innerText = '-';
                            stockDisplay.innerText = '-';
                        }
                    });
            } else {
                priceDisplay.innerText = '-';
                stockDisplay.innerText = '-';
            }
        }

        selects.forEach(select => {
            select.addEventListener('change', () => filterDropdowns(select));
        });
    </script> --}}

    {{-- Without disabling feature --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.attribute-dropdown');
            const priceDisplay = document.getElementById('variant-price');
            const stockDisplay = document.getElementById('variant-stock');

            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('change', function() {
                    const selectedValues = [];

                    dropdowns.forEach(dd => {
                        const val = dd.value;
                        if (val) selectedValues.push(val);
                    });

                    if (selectedValues.length === dropdowns.length) {
                        fetch("{{ route('products.getVariant') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    product_id: {{ $product->id }},
                                    attribute_value_ids: selectedValues
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.price) {
                                    priceDisplay.innerText = `$${data.price}`;
                                    stockDisplay.innerText = data.stock;
                                } else {
                                    priceDisplay.innerText = '-';
                                    stockDisplay.innerText = '-';
                                }
                            });
                    } else {
                        priceDisplay.innerText = '-';
                        stockDisplay.innerText = '-';
                    }
                });
            });
        });
    </script> --}}
</body>

</html>
