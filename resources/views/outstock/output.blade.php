@extends('layouts.master')

@section('title', 'Kurangi Barang')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Kurangi Barang</h1>

    <form action="{{ route('out-stocks.batchStore') }}" method="POST" enctype="multipart/form-data">
        @csrf
    
        <div class="form-group">
            <label for="date">Tanggal</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
    
        <div class="form-group">
            <label for="excel">Import dari Excel</label>
            <input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx">
        </div>

        <button type="submit" class="btn btn-primary mb-4">Simpan Semua</button>
    
        <table class="table table-bordered" id="OutStockTable">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Produk</th>
                    <th>Batch (Tanggal Exp & Sisa)</th>
                    <th>Jumlah (Quantity)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td>
                        <input type="text" name="items[0][code]" class="form-control code-input" required>
                    </td>
                    <td>
                        <input type="text" class="form-control" value="-" readonly>
                    </td>
                    <td>
                        <select name="items[0][in_detail_id]" class="form-control batch-select" required>
                            <option value="">Pilih Batch</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[0][quantity]" class="form-control" required>
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
    let rowIndex = 1;

    document.getElementById('addRow').addEventListener('click', function () {
        addRow('', '');
    });
// Event delegation utk input kode barang
document.getElementById('tableBody').addEventListener('change', async function (e) {
    if (e.target.classList.contains('code-input')) {
        const row = e.target.closest('tr');
        const code = e.target.value;

        if (!code) return;

        try {
            const res = await fetch(`{{ route('out-stocks.getBatches') }}?code=${encodeURIComponent(code)}`);
            const data = await res.json();

            // isi select batch
            const select = row.querySelector('.batch-select');
            select.innerHTML = '<option value="">Pilih Batch</option>';
            data.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch.id;
                option.textContent = `${batch.expiry_date} (sisa: ${batch.remaining_stock})`;
                select.appendChild(option);
            });

        } catch (err) {
            alert('Gagal mengambil batch untuk kode: ' + code);
            console.error(err);
        }
    }
});

    document.getElementById('tableBody').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    function addRow(code, quantity, name = '-', batchOptions = []) {
    let optionsHtml = '<option value="">Pilih Batch</option>';
    batchOptions.forEach(opt => {
        optionsHtml += `<option value="${opt.id}">${opt.expiry_date} (sisa: ${opt.remaining_stock})</option>`;
    });

    const newRow = `
        <tr>
            <td><input type="text" name="items[${rowIndex}][code]" class="form-control code-input" value="${code}" required></td>
            <td><input type="text" class="form-control" value="${name}" readonly></td>
            <td>
                <select name="items[${rowIndex}][in_detail_id]" class="form-control batch-select" required>
                    ${optionsHtml}
                </select>
            </td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control" value="${quantity}" required></td>
            <td><button type="button" class="btn btn-danger removeRow">Hapus</button></td>
        </tr>`;
    document.getElementById('tableBody').insertAdjacentHTML('beforeend', newRow);
    rowIndex++;
}


    document.querySelector('input[name="excel_file"]').addEventListener('change', function () {
        const formData = new FormData();
        formData.append('excel_file', this.files[0]);

        fetch("{{ route('out-stocks.previewExcel') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
        
            document.getElementById('tableBody').innerHTML = '';
            rowIndex = 0;

            data.forEach(async (item) => {
                if (item.quantity > 0) {
                    try {
                        const res = await fetch(`{{ route('out-stocks.getBatches') }}?code=${encodeURIComponent(item.code)}`);
                        const batches = await res.json();
                        addRow(item.code, item.quantity, item.name, batches);
                    } catch (err) {
                        addRow(item.code, item.quantity, item.name);
                    }
                }
            });
        })
        .catch(error => {
            alert('Gagal membaca file Excel. Pastikan formatnya benar.');
            console.error(error);
        });
    });
</script>
@endpush

