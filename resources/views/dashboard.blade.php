@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <!-- Content -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Produk Teratas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"> {{ $topOut?->product->name ?? '-' }}</div>
                        <div class="text-xs mt-1 text-muted text-uppercase mb-1">Terjual {{ $topOut?->total ?? 0 }} pcs minggu ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pembelian Terbanyak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"> {{ $topIn?->product->name ?? '-' }}</div>
                        <div class="text-xs mt-1 text-muted text-uppercase mb-1"> Barang Masuk {{ $topIn?->total ?? 0 }} pcs minggu ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Terendah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"> {{ $lowestStock->name ?? '-' }}</div>
                        <div class="text-xs mt-1 text-muted text-uppercase mb-1"> {{ $lowestStock->stock ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Stok Minimum --}}
<div>
        <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">STOK PRODUK DIBAWAH MINIMUM</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Produk</th>
                                <th>Stok Minimal</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>NO</th>
                                <th>Produk</th>
                                <th>Stok Minimal</th>
                                <th>Stok</th>
                            </tr>
                        </tfoot>
                       <tbody>
                        @foreach ($lowStocks as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->min_stock }}</td>
                                <td>{{ $product->stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Expired --}}

{{-- Tabel Stok Minimum --}}
<div>
        <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">STOK PRODUK SUDAH KEDALUWARSA</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kedaluwarsa</th>
                            <th>Sisa Stok</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Kedaluwarsa</th>
                            <th>Sisa Stok</th>
                            </tr>
                        </tfoot>
                       <tbody>
                        @foreach ($expiredStocks as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->expiry_date }}</td>
                            <td>{{ $item->remaining_stock }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Chart Section --}}

<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan Bulan ini</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                    @push('scripts')
                    <script>
                    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
                    Chart.defaults.global.defaultFontColor = '#858796';

                    const ctxArea = document.getElementById("myAreaChart");
                    const myLineChart = new Chart(ctxArea, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($areaLabels) !!}, // ['Week 1', 'Week 2', ...]
                        datasets: [{
                        label: "Penjualan",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: {!! json_encode($areaValues) !!}, // [10, 20, 15, ...]
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                        scales: {
                        xAxes: [{
                            time: { unit: 'week' },
                            gridLines: { display: false, drawBorder: false },
                            ticks: { maxTicksLimit: 6 }
                        }],
                        yAxes: [{
                            ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return value + ' pcs'; // ganti '$' dengan satuan kamu
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
                        legend: { display: false },
                        tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                            const datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + tooltipItem.yLabel + ' pcs';
                            }
                        }
                        }
                    }
                    });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Penjualan dan Pembelian</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                        @push('scripts')
                        <script>
                        const ctxPie = document.getElementById("myPieChart");
                        const myPieChart = new Chart(ctxPie, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($pieLabels) !!}, // ["Masuk", "Keluar"]
                            datasets: [{
                            data: {!! json_encode($pieValues) !!},  // [150, 120]
                            backgroundColor: ['#4e73df', '#1cc88a'],
                            hoverBackgroundColor: ['#2e59d9', '#17a673'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            caretPadding: 10,
                            },
                            legend: {
                            display: false
                            },
                            cutoutPercentage: 80,
                        },
                        });
                        </script>
                        @endpush

                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Pembelian
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Penjualan
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
