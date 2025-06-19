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




    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        <div class="container my-4">
            <h3>Add Product</h3>

            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Base Price (optional)</label>
                <input type="number" name="base_price" step="0.01" class="form-control">
            </div>

            <hr>
            <h4>Variants</h4>
            <div id="variant-wrapper">
                <div class="variant-group border p-3 mb-3 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2 remove-variant"
                        style="display: none;"></button>

                    <div class="mb-2">
                        <label class="form-label">SKU</label>
                        <input type="text" name="variants[0][sku]" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Price</label>
                        <input type="number" name="variants[0][price]" step="0.01" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Stock</label>
                        <input type="number" name="variants[0][stock]" class="form-control" required>
                    </div>

                    {{-- Attribute Dropdowns --}}
                    @foreach ($attributes as $attribute)
                        <div class="mb-2">
                            <label class="form-label">{{ $attribute->name }}</label>
                            <select name="variants[0][attribute_value_ids][]" class="form-select" required>
                                <option value="">Select {{ $attribute->name }}</option>
                                @foreach ($attribute->values as $value)
                                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>


            <button type="button" id="add-variant" class="btn btn-secondary mb-3">Add More Variant</button>
            <br>
            <button type="submit" class="btn btn-primary">Create Product</button>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let variantIndex = 1;

            document.getElementById('add-variant').addEventListener('click', function() {
                const wrapper = document.getElementById('variant-wrapper');
                const lastGroup = wrapper.querySelector('.variant-group');
                const newGroup = lastGroup.cloneNode(true);

                // Update input names
                newGroup.querySelectorAll('input, select').forEach(input => {
                    if (input.name.includes('variants[0]')) {
                        input.name = input.name.replace('variants[0]', `variants[${variantIndex}]`);
                        input.value = ''; // clear value
                    }
                });
                newGroup.querySelectorAll('input, select').forEach(input => {
                    input.name = input.name.replace(/variants\\[\\d+\\]/,
                        `variants[${variantIndex}]`);
                    if (input.type !== 'hidden') input.value = '';
                })
                newGroup.querySelector('.remove-variant').style.display = 'block';
                wrapper.appendChild(newGroup);
                variantIndex++;
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-variant')) {
                    const group = e.target.closest('.variant-group');
                    group.remove();
                }
            });
        });
    </script>
</body>

</html>
