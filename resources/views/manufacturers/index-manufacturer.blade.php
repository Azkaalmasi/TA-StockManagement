@extends('layouts.master')

@section('title', 'Distributor')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Distributor</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kategori Produk</h6>
            <a href="{{ route('manufacturers.create') }}" class="btn btn-primary">
                + Tambah Distributor
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama Distributor</th>
                            <Th>Alamat</Th>
                            <th>Nomor Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($manufacturers as $index => $manufacturer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $manufacturer->name }}</td>
                            <td>{{ $manufacturer->address}}</td>
                            <td>{{ $manufacturer->phone }}</td>
                            <td>
                                <a href="{{ route('manufacturers.edit', $manufacturer->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('manufacturers.destroy', $manufacturer->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
