<tr>
    <td class="text-center">
        <select name="product_id[]" class="form-select" onchange="checkDuplicate(this)">
            <option value="">Select One</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="text-center">
        <input type="text" name="quantity[]" class="form-control" placeholder="1">
    </td>
    <td class="text-center">
        <a href="javascript:void(0)" onclick="removeRow(this)" class="d-inline-block btn btn-sm btn-danger rounded">⨯ Remove</a>
    </td>
</tr>
