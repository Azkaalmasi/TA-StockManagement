@extends('layouts.master')

@section('title', 'Edit Distributor')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Distributor</h1>

    <form action="{{ route('manufacturers.update', $manufacturer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Distributor</label>
            <input type="text" class="form-control" name="name" value="{{ $manufacturer->name }}" required>
        </div>

        <div class="form-group">
            <label for="name">Alamat</label>
            <input type="text" class="form-control" name="address" value="{{ $manufacturer->address }}" required>
        </div>

        <div class="form-group">
            <label for="name">Nomor Telepon</label>
            <input type="text" class="form-control" name="phone" value="{{ $manufacturer->phone}}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
