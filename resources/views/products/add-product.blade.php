@extends('layouts.master')

@section('title', 'Tambah Produk')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Produk Baru</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="form-group">
                <label for="code">Kode Produk</label>
                <input type="text" class="form-control" name="code" required>
            </div>

            <div class="form-group">
                <label for="category_id">Kategori</label>
                <select class="form-control" name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="stock">Stok</label>
                <input type="number" class="form-control" name="stock" required>
            </div>

            <div class="form-group">
                <label for="min_stock">Stok Minimal</label>
                <input type="number" class="form-control" name="min_stock" required>
            </div>

            <div class="form-group">
                <label for="pcs_per_box">Jumlah per Box</label>
                <input type="number" class="form-control" name="pcs_per_box" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
