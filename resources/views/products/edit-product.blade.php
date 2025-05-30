@extends('layouts.master')

@section('title', 'Edit Produk')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Produk</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
        </div>

        <div class="form-group">
            <label for="code">Kode Produk</label>
            <input type="text" class="form-control" name="code" value="{{ $product->code }}" required>
        </div>

        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select class="form-control" name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="stock">Stok</label>
            <input type="number" class="form-control" name="stock" value="{{ $product->stock }}" required>
        </div>

        <div class="form-group">
            <label for="min_stock">Stok Minimal</label>
            <input type="number" class="form-control" name="min_stock" value="{{ $product->min_stock }}" required>
        </div>

        <div class="form-group">
            <label for="pcs_per_box">PCS per Box</label>
            <input type="number" class="form-control" name="pcs_per_box" value="{{ $product->pcs_per_box }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
