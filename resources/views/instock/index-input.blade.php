@extends('layouts.master')

@section('title', 'Stok Masuk')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Stok Masuk</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Stok Masuk Tersedia</h6>
            <a href="{{ route('in-stocks.create') }}" class="btn btn-primary">
                + Tambah Stok Masuk
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Tanggal Masuk</th>
                            <th>Quantity Masuk</th>
                            <th>Distributor</th>
                            <th>Kedaluwarsa</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Tanggal Masuk</th>
                            <th>Quantity Masuk</th>
                            <th>Distributor</th>
                            <th>Kedaluwarsa</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @forelse($inDetails as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->product->name ?? '-' }}</td>
                                <td>{{ $detail->inStock->date ? $detail->inStock->date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ $detail->manufacturer->name ?? '-' }}</td>
                                <td>{{ $detail->expiry_date }}</td>
                                <td>{{ $detail->inStock->user->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('products.show', $detail->product->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                             
                                    {{-- <form action="#" method="POST" style="display:inline-block;">
                                        @csrf
                                      
                                        <button type="submit" class="btn btn-danger btn-sm" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data stok masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection