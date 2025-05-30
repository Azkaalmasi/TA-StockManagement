@extends('layouts.master')

@section('title', 'Detail Produk')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Produk</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    {{-- Informasi Produk --}}
    <div class="card mb-4">
        <div class="card-header font-weight-bold">
            Informasi Produk
        </div>
        <div class="card-body">
            <p><strong>Nama Produk:</strong> {{ $product->name }}</p>
            <p><strong>Kode Produk:</strong> {{ $product->code }}</p>
            <p><strong>Stok Sekarang:</strong> {{ $product->stock }}</p>
            <p><strong>Stok Minimal:</strong> {{ $product->min_stock }}</p>
            <p><strong>Isi per Box:</strong> {{ $product->pcs_per_box }}</p>
            <p><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>
        </div>
    </div>

    {{-- Histori Stok Masuk --}}
    <div class="card mb-4">
        <div class="card-header font-weight-bold text-success">
            Histori Stok Masuk
        </div>
        <div class="card-body">
            @if ($inDetails->count())
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Masuk</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->inStock->date ?? '-' }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->inStock->user->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada data stok masuk untuk produk ini.</p>
            @endif
        </div>
    </div>

    {{-- Histori Stok Keluar --}}
    <div class="card mb-5">
        <div class="card-header font-weight-bold text-danger">
            Histori Stok Keluar
        </div>
        <div class="card-body">
            @if ($outDetails->count())
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Keluar</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($outDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->outStock->date ?? '-' }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->outStock->user->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada data stok keluar untuk produk ini.</p>
            @endif
        </div>
    </div>

</div>
@endsection
