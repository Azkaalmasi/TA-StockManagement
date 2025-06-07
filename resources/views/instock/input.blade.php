@extends('layouts.master')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tambah Barang Masuk</h1>

    <form action="{{ route('in-stocks.batchStore') }}" method="POST" enctype="multipart/form-data">
        @csrf
    
        <div class="form-group">
            <label for="date">Tanggal</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
    
        <div class="form-group mb-4">
            <label for="excel">Import dari Excel</label>
            <input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx">
        </div>
        <button type="submit" class="btn mb-4 btn-primary">Simpan Semua</button>

        <table class="table table-bordered" id="inStockTable">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Produk</th>
                    <th>Jumlah (Quantity)</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Distributor</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td>
                        <input type="text" name="items[0][code]" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" value="-" readonly>
                    </td>
                    <td>
                        <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="date" name="items[0][expiry_date]" class="form-control" required>
                    </td>
                    <td>
                        <select name="items[0][manufacturer_id]" class="form-control" required>
                            <option value="">— Pilih Distributor —</option>
                            @foreach($manufacturers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger removeRow">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary" id="addRow">Tambah Baris</button>


    </form>
</div>
@endsection

@push('scripts')
<script>

    const manufacturers = @json($manufacturers);
    let rowIndex = 1;

    function makeManufacturerSelect(name) {
        let html = `<select name="${name}" class="form-control" required>`;
        html += `<option value="">— Pilih Produsen —</option>`;
        manufacturers.forEach(m => {
            html += `<option value="${m.id}">${m.name}</option>`;
        });
        html += `</select>`;
        return html;
    }
    function addRow(code = '', qty = 1, name = '-', expiry = '') {
        const i = rowIndex++;
        const row = `
            <tr>
                <td><input type="text" name="items[${i}][code]" class="form-control" value="${code}" required></td>
                <td><input type="text" class="form-control" value="${name}" readonly></td>
                <td><input type="number" name="items[${i}][quantity]" class="form-control" value="${qty}" min="1" required></td>
                <td><input type="date" name="items[${i}][expiry_date]" class="form-control" value="${expiry}" required></td>
                <td>${makeManufacturerSelect(`items[${i}][manufacturer_id]`)}</td>
                <td><button type="button" class="btn btn-danger removeRow">Hapus</button></td>
                <input type="hidden" name="items[${i}][remaining_stock]" value="${qty}">
            </tr>`;
        document.getElementById('tableBody').insertAdjacentHTML('beforeend', row);
    }

    document.getElementById('addRow').addEventListener('click', () => addRow());

    document.getElementById('tableBody').addEventListener('click', e => {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    // Preview Excel: generate rows tanpa expiry & manufacturer
    document.querySelector('input[name="excel_file"]').addEventListener('change', function () {
        const formData = new FormData();
        formData.append('excel_file', this.files[0]);

        fetch("{{ route('in-stocks.previewExcel') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('tableBody').innerHTML = '';
            rowIndex = 0;
            data.forEach(item => {
                if (item.quantity > 0) {
                    addRow(item.code, item.quantity, item.name);
                }
            });
        })
        .catch(err => {
            alert('Gagal membaca file Excel. Pastikan formatnya benar.');
            console.error(err);
        });
    });
</script>
@endpush
