<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Storage;
class BarangController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->search;

        $barang = Barang::when($search, function ($query) use ($search) {

            $query->where('produk', 'like', "%{$search}%")
                ->orWhere('kategori', 'like', "%{$search}%")
                ->orWhere('harga', 'like', "%{$search}%");

        })->latest()->paginate(10);

        return view('admin.admin', compact(
            'barang',
            'search'
        ));
    }



    public function store(Request $request)
    {
        $thumbnail = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('produk', 'public');
        }

        Barang::create([
            'thumbnail' => $thumbnail,
            'kategori' => $request->kategori,
            'produk' => $request->produk,
            'harga' => $request->harga,
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'kategori' => 'required',
            'produk' => 'required',
            'harga' => 'required|numeric',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {

            if ($barang->thumbnail && Storage::disk('public')->exists($barang->thumbnail)) {
                Storage::disk('public')->delete($barang->thumbnail);
            }

            $barang->thumbnail = $request->file('thumbnail')->store('produk', 'public');
        }

        $barang->kategori = $request->kategori;
        $barang->produk = $request->produk;
        $barang->harga = $request->harga;

        $barang->save();

        return redirect()->back()->with('success', 'Data berhasil diubah.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        // Hapus gambar
        if ($barang->thumbnail && Storage::disk('public')->exists($barang->thumbnail)) {
            Storage::disk('public')->delete($barang->thumbnail);
        }

        $barang->delete();

        return redirect()->route('admin.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
