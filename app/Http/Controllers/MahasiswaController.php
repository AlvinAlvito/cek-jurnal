<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index()
    {
        $q = request('q');
        $perPage = (int) (request('per_page') ?? 100);

        $query = Mahasiswa::query()->orderByDesc('id');
        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('nim', 'like', "%{$q}%");
            });
        }

        $mahasiswa = $query->paginate($perPage)->appends(request()->query());

        if (request()->wantsJson()) {
            return response()->json($mahasiswa);
        }

        return view('admin.data-mahasiswa', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $mahasiswa,
            'q'         => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nim'  => ['required', 'string', 'max:50', 'unique:mahasiswa,nim'],
        ]);

        $mhs = Mahasiswa::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Mahasiswa berhasil ditambahkan', 'data' => $mhs], 201);
        }

        return redirect()->back()->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $mhs = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nim'  => [
                'required', 'string', 'max:50',
                Rule::unique('mahasiswa', 'nim')->ignore($mhs->id),
            ],
        ]);

        $mhs->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Mahasiswa berhasil diperbarui', 'data' => $mhs]);
        }

        return redirect()->back()->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mhs = Mahasiswa::findOrFail($id);

        try {
            $mhs->delete();
        } catch (\Throwable $e) {
            $msg = 'Mahasiswa tidak dapat dihapus karena sudah terpakai pada data pemakaian.';
            if (request()->wantsJson()) {
                return response()->json(['message' => $msg], 409);
            }
            return redirect()->back()->with('error', $msg);
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Mahasiswa berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
