<?php

namespace App\Http\Controllers;

use App\Models\RumahJurnal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JurnalController extends Controller
{
    public function index()
    {
        $q = request('q');
        $perPage = (int) (request('per_page') ?? 10);

        $query = RumahJurnal::query()->orderByDesc('id');
        if (!empty($q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('link', 'like', "%{$q}%");
            });
        }

        $jurnal = $query->paginate($perPage)->appends(request()->query());

        if (request()->wantsJson()) {
            return response()->json($jurnal);
        }

        return view('admin.data-jurnal', [
            'title'  => 'Data Rumah Jurnal',
            'jurnal' => $jurnal,
            'q'      => $q,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:150'],
            'link'       => ['required', 'string', 'max:255', 'url', 'unique:rumah_jurnal,link'],
            'deskripsi'  => ['nullable', 'string'],
        ]);

        $data = RumahJurnal::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Jurnal berhasil ditambahkan', 'data' => $data], 201);
        }

        return redirect()->back()->with('success', 'Jurnal berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $row = RumahJurnal::findOrFail($id);

        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:150'],
            'link'       => [
                'required', 'string', 'max:255', 'url',
                Rule::unique('rumah_jurnal', 'link')->ignore($row->id),
            ],
            'deskripsi'  => ['nullable', 'string'],
        ]);

        $row->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Jurnal berhasil diperbarui', 'data' => $row]);
        }

        return redirect()->back()->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $row = RumahJurnal::findOrFail($id);

        try {
            $row->delete();
        } catch (\Throwable $e) {
            $msg = 'Jurnal tidak dapat dihapus.';
            if (request()->wantsJson()) {
                return response()->json(['message' => $msg], 409);
            }
            return redirect()->back()->with('error', $msg);
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Jurnal berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Jurnal berhasil dihapus.');
    }
}
