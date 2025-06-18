@extends('layouts.master')

@section('title', 'Tambah Distributor')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Distributor Baru</h1>

    <form action="{{ route('manufacturers.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Nama Distributor</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="form-group">
            <label for="name">Alamat</label>
            <input type="text" class="form-control" name="address" required>
        </div>

        <div class="form-group">
            <label for="name">Nomor Telepon</label>
            <input type="text" class="form-control" name="phone" required>
        </div>



        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
