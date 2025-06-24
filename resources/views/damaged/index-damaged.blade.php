@extends('layouts.master')

@section('title', 'Barang Rusak')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Barang Rusak</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Barang Rusak</h6>
            <a href="{{ route('damaged-stocks.create') }}" class="btn btn-primary">
                + Tambah Barang Rusak
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <form method="GET" class="form-inline mb-3">
                    <label class="mr-2">Filter:</label>
                    @php
                        $selectedMonth = request()->has('month') ? request('month') : now()->month;
                    @endphp

                    <select name="month" class="form-control mr-2">
                        <option value="" {{ request('month') === '' ? 'selected' : '' }}>Semua Bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int)$selectedMonth === $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>

                   <select name="year" class="form-control mr-2">
                        @foreach ($availableYears as $y)
                            <option value="{{ $y }}" {{ (int) request('year', now()->year) === (int) $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>


                    <button type="submit" class="btn btn-primary">Terapkan</button>
                </form>

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Tanggal Rusak</th>
                            <th>Jumlah Rusak</th>
                            <th>Distributor</th>
                            <th>Kedaluwarsa</th>
                            <th>Petugas</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Tanggal Rusak</th>
                            <th>Jumlah Rusak</th>
                            <th>Distributor</th>
                            <th>Kedaluwarsa</th>
                            <th>Petugas</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @forelse($damagedDetails as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->product->name ?? '-' }}</td>
                                <td>{{ $detail->damagedStock->date ?? '-' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ $detail->inDetail->manufacturer->name ?? '-' }}</td>
                                <td>{{ $detail->inDetail->expiry_date ?? '-' }}</td>
                                <td>{{ $detail->damagedStock->user->name ?? '-' }}</td>
                                <td>{{ $detail->information }}</td>
                                <td>
                                    <a href="{{ route('products.show', $detail->product->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data barang rusak.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
