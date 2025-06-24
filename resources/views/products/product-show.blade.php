@extends('layouts.master')

@section('title', 'Detail Produk')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Produk</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    <div class="container-fluid">
        <a href="{{ route('products.export.pdf', $product->id) }}" class="btn btn-success mb-3">
         <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

     {{-- Forecasting (Estimasi Minggu Depan) --}}
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header font-weight-bold text-primary">
                    Estimasi Pengeluaran Minggu Depan
                </div>
                <div class="card-body">
                    @if (isset($forecast))
                        <p>Perkiraan jumlah stok yang dibutuhkan minggu depan: <strong>{{ $forecast }}</strong> unit</p>
                    @else
                        <p class="text-muted">Data histori belum cukup untuk melakukan forecasting.</p>
                    @endif
                </div>
            </div>
        </div>

    {{-- Chart Penjualan Per Minggu --}}
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header font-weight-bold text-info">
                Grafik Penjualan Mingguan
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="weeklySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Produk --}}
    <div class="container-fluid">
    <div class="card shadow mb-4">
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
    </div>

    {{-- Histori Stok Masuk --}}
    <div>
        <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="mb-3 font-weight-bold text-primary">RIWAYAT STOK MASUK</h6>
                <form method="GET" class="form-inline mb-1">
                <label class="mr-2">Filter:</label>

                <select name="month" class="form-control mr-2">
                    <option value="">Semua Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>

               <select name="year" class="form-control mr-2">
                    @foreach ($allYears as $y)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>


                <button type="submit" class="btn btn-primary">Terapkan</button>
            </form>
            </div>
            <div class="card-body">
                 @if ($inDetails->count())
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Masuk</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Masuk</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </tfoot>
                       <tbody>
                            @foreach ($inDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ optional($detail->inStock)->date->format('Y-m-d') ?? '-' }}</td>
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
    </div>
</div>

    {{-- Histori Stok Keluar --}}

    <div>
        <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="mb-3 font-weight-bold text-primary">RIWAYAT STOK KELUAR</h6>
            <form method="GET" class="form-inline mb-1">
                <label class="mr-2">Filter:</label>

                <select name="month" class="form-control mr-2">
                    <option value="">Semua Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>

                <input type="number" name="year" class="form-control mr-2" placeholder="Tahun"
                    value="{{ request('year', now()->year) }}" min="2000" max="{{ now()->year }}">

                <button type="submit" class="btn btn-primary">Terapkan</button>
            </form>
            </div>
            <div class="card-body">
                  @if ($outDetails->count())
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Keluar</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Keluar</th>
                                <th>Jumlah</th>
                                <th>Petugas</th>
                            </tr>
                        </tfoot>
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
</div>

@push('scripts')
<script>
    Chart.defaults.global.defaultFontFamily = 'Nunito';
    Chart.defaults.global.defaultFontColor = '#858796';

    const labels = {!! json_encode($chartLabels) !!};
    const fullData = {!! json_encode($chartData) !!};
    const lastIndex = fullData.length - 1;

   const actualData = fullData.slice(0, fullData.length - 1);
    const forecastPoint = fullData[fullData.length - 1];
    const forecastData = [...actualData, forecastPoint]; 



    const ctx = document.getElementById("weeklySalesChart");
    const weeklyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
                datasets: [
                {
                    label: "Jumlah Terjual",
                    data: actualData,
                    borderColor: "rgba(78, 115, 223, 1)",
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    borderWidth: 2,
                    lineTension: 0.3,
                },
                {
                    label: "Forecast",
                    data: forecastData.map((val, idx) =>
                    idx < forecastData.length - 2 ? null : val
                    ),
                    borderColor: "rgba(255, 99, 132, 1)",
                    backgroundColor: "rgba(255, 99, 132, 0.2)",
                    pointBackgroundColor: "rgba(255, 99, 132, 1)",
                    pointRadius: 5,
                    borderDash: [5, 5],
                    fill: false,
                },
                ],


        },
        options: {
            maintainAspectRatio: false,
            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 }},
            scales: {
                xAxes: [{
                    gridLines: { display: false, drawBorder: false },
                    ticks: { maxTicksLimit: 7 }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return value + ' pcs';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }]
            },
            legend: { display: true },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: true,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        return tooltipItem.dataset.label + ': ' + tooltipItem.yLabel + ' pcs';
                    }
                }
            }
        }
    });
</script>
@endpush


@endsection
