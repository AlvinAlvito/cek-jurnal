<?php

namespace App\Http\Controllers;

use App\Models\DosenPembimbing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    public function index()
    {
        $q = request('q');
        $perPage = (int) (request('per_page') ?? 100);

        $query = DosenPembimbing::query()->orderByDesc('id');
        if (!empty($q)) {
            $query->where('nama', 'like', "%{$q}%");
        }

        $dosen = $query->paginate($perPage)->appends(request()->query());

        if (request()->wantsJson()) {
            return response()->json($dosen);
        }

        return view('admin.data-dosen', [
            'title' => 'Data Dosen Pembimbing',
            'dosen' => $dosen,
            'q'     => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150', 'unique:dosen_pembimbing,nama'],
        ]);

        $dosen = DosenPembimbing::create([
            'nama' => $validated['nama'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dosen berhasil ditambahkan', 'data' => $dosen], 201);
        }

        return redirect()->back()->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dosen = DosenPembimbing::findOrFail($id);

        $validated = $request->validate([
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique('dosen_pembimbing', 'nama')->ignore($dosen->id),
            ],
        ]);

        $dosen->update(['nama' => $validated['nama']]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Dosen berhasil diperbarui', 'data' => $dosen]);
        }

        return redirect()->back()->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dosen = DosenPembimbing::findOrFail($id);

        try {
            $dosen->delete();
        } catch (\Throwable $e) {
            $msg = 'Dosen tidak dapat dihapus karena sudah terpakai pada data pemakaian.';
            if (request()->wantsJson()) {
                return response()->json(['message' => $msg], 409);
            }
            return redirect()->back()->with('error', $msg);
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Dosen berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Dosen berhasil dihapus.');
    }
}
