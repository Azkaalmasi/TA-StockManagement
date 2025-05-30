@extends('layouts.master')

@section('title', 'Import Data from Excel')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Import Data from Excel</h6>
    </div>
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="excelFile">Upload Excel File</label>
                <input type="file" class="form-control" name="excelFile" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>

@if(session('data'))
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Sebelum Submit</h6>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            @foreach(session('data')[0] as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i < count(session('data')); $i++)
                            <tr>
                                @foreach(session('data')[$i] as $key => $value)
                                    <td>
                                        <input type="text" name="data[{{ $i }}][{{ $key }}]" class="form-control" value="{{ $value }}">
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success mt-3">Submit Data</button>
        </form>
    </div>
</div>
@endif
@endsection
