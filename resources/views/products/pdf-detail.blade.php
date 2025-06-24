<div style="text-align: center; margin-bottom: 40px;">
    <img src="{{ public_path('img/center.png') }}" alt="Logo" style="height: 200px;">
</div>

<h2>Detail Produk: {{ $product->name }}</h2>
<p><strong>Kode:</strong> {{ $product->code }}</p>
<p><strong>Stok Sekarang:</strong> {{ $product->stock }}</p>
<p><strong>Stok Minimal:</strong> {{ $product->min_stock }}</p>
<p><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>

<h4>Riwayat Stok Masuk</h4>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Masuk</th>
            <th>Jumlah</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($inDetails as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($detail->inStock->date)->format('d-m-Y') }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->inStock->user->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Riwayat Stok Keluar</h4>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Keluar</th>
            <th>Jumlah</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($outDetails as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($detail->outStock->date)->format('d-m-Y') }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->outStock->user->name ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
