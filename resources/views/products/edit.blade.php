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
    <form method="POST" action="{{ route('products.update', $product) }}" class="container mt-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-bold">Product Name</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Description</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Base Price</label>
            <input type="number" name="base_price" step="0.01" class="form-control"
                value="{{ $product->base_price }}">
        </div>

        <hr class="my-4">
        <h4 class="mb-3">Variants</h4>

        <div id="variant-wrapper">
            @foreach ($product->variants as $index => $variant)
                <div class="variant-group border rounded p-3 mb-4 bg-light position-relative">
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <input type="text" name="variants[{{ $index }}][sku]" class="form-control"
                                value="{{ $variant->sku }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Price</label>
                            <input type="number" name="variants[{{ $index }}][price]" class="form-control"
                                step="0.01" value="{{ $variant->price }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock</label>
                            <input type="number" name="variants[{{ $index }}][stock]" class="form-control"
                                value="{{ $variant->stock }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        @foreach ($attributes as $attribute)
                            <div class="col-md-4">
                                <label class="form-label">{{ $attribute->name }}</label>
                                <select name="variants[{{ $index }}][attribute_value_ids][]"
                                    class="form-select">
                                    @foreach ($attribute->values as $value)
                                        <option value="{{ $value->id }}"
                                            @if ($variant->values->contains($value)) selected @endif>
                                            {{ $value->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-danger btn-sm mt-3 remove-variant">Remove Variant</button>
                </div>
            @endforeach
        </div>

        <div class="mb-4">
            <button type="button" class="btn btn-secondary" id="add-variant">+ Add Variant</button>
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let variantIndex = {{ $product->variants->count() }};

        $('#add-variant').click(function() {
            let variantHtml = `
        <div class="variant-group border p-2 mb-2">
            <div class="row g-3">
                 <div class="col-md-4">
                <label  class="form-label">SKU:</label>
                <input type="text" name="variants[${variantIndex}][sku]" required  class="form-control">
                </div>
                <div class="col-md-4">
                <label  class="form-label">Price:</label>
                <input type="number" name="variants[${variantIndex}][price]" step="0.01" required class="form-control">
                </div>
                <div class="col-md-4">
                <label  class="form-label">Stock:</label>
                <input type="number" name="variants[${variantIndex}][stock]" required class="form-control">
                </div>
            </div>
            <div class="row mt-3">
            @foreach ($attributes as $attribute)
                <div class="col-md-4 mb-2">
                <label class="form-label">{{ $attribute->name }}</label>
                <select name="variants[${variantIndex}][attribute_value_ids][]" class="form-select">
                    @foreach ($attribute->values as $value)
                        <option value="{{ $value->id }}">{{ $value->value }}</option>
                    @endforeach
                </select>
                </div>
            @endforeach
             </div>
            <button type="button" class="btn btn-danger btn-sm mt-3 remove-variant">Remove</button>
        </div>
    `;
            $('#variant-wrapper').append(variantHtml);
            variantIndex++;
        });

        $(document).on('click', '.remove-variant', function() {
            $(this).closest('.variant-group').remove();
        });
    </script>

</body>

</html>
