<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManufacturerController extends Controller
{
    public function index()
    {
        $manufacturers = Manufacturer::all();
        return view('manufacturers.index-manufacturer', compact('manufacturers'));
    }

    public function create()
    {
        return view('manufacturers.add-manufacturer');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
        ]);

        $validated['id'] = (string) Str::uuid();

        Manufacturer::create($validated);

        return redirect()->route('manufacturers.index')->with('success', 'Produsen berhasil ditambahkan.');
    }

     public function edit($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        return view('manufacturers.edit-manufacturer', compact('manufacturer'));
    }

    public function update(Request $request, $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
        ]);

        $manufacturer->update($validated);

        return redirect()->route('manufacturers.index')->with('success', 'Produsen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->delete();

        return redirect()->route('manufacturers.index')->with('success', 'Produsen berhasil dihapus.');
    }
}

