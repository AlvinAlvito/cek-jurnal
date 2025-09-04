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
        $perPage = (int) (request('per_page') ?? 100);
        $bulan = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];

        $query = RumahJurnal::query()->orderByDesc('id');

        if (!empty($q)) {
            $qLower = strtolower(trim($q));
            $query->where(function ($w) use ($q, $qLower, $bulan) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('link', 'like', "%{$q}%")
                  ->orWhere('sinta', (int) $q ?: null)
                  ->orWhere('tahun_akreditasi', (int) $q ?: null);

                if (in_array($qLower, $bulan, true)) {
                    $w->orWhereJsonContains('edisi', $qLower);
                }
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
        $bulan = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];

        $validated = $request->validate([
            'nama'              => ['required', 'string', 'max:150'],
            'link'              => ['required', 'string', 'max:255', 'url', 'unique:rumah_jurnal,link'],
            'sinta'             => ['required', 'integer', 'min:1', 'max:6'],
            'tahun_akreditasi'  => ['required', 'integer', 'between:2000,2100'],
            'edisi'             => ['required', 'array', 'min:1'],
            'edisi.*'           => [Rule::in($bulan)],
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
        $bulan = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];

        $validated = $request->validate([
            'nama'              => ['required', 'string', 'max:150'],
            'link'              => [
                'required', 'string', 'max:255', 'url',
                Rule::unique('rumah_jurnal', 'link')->ignore($row->id),
            ],
            'sinta'             => ['required', 'integer', 'min:1', 'max:6'],
            'tahun_akreditasi'  => ['required', 'integer', 'between:2000,2100'],
            'edisi'             => ['required', 'array', 'min:1'],
            'edisi.*'           => [Rule::in($bulan)],
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
