<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LapanganController extends Controller
{
    private function api()
    {
        return Http::withToken(session('token'))->acceptJson();
    }

    private function getLapanganList()
    {
        try {
            $res = $this->api()->get(env('API_URL') . '/api/lapangan');
            if ($res->successful()) {
                return $res->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
        }

        return [];
    }

    // GET LIST
    public function index()
    {
        $lapangan = $this->getLapanganList();

        return view('admin.pages.lapangan', compact('lapangan'));
    }

    // CREATE
    public function store(Request $req)
    {
        $req->validate([
            'nama_lapangan' => 'required|string',
        ]);

        $inputNama = strtolower(trim($req->nama_lapangan));
        $existingLapangan = $this->getLapanganList();

        $isDuplicate = collect($existingLapangan)->contains(function ($item) use ($inputNama) {
            return strtolower(trim($item['nama_lapangan'] ?? '')) === $inputNama;
        });

        if ($isDuplicate) {
            return back()->with('error', 'Nama lapangan "' . $req->nama_lapangan . '" sudah ada! Gunakan nama lain.')->withInput();
        }

        try {
            $res = $this->api()->post(env('API_URL') . '/api/lapangan', [
                'nama_lapangan' => $req->nama_lapangan,
                'harga_pagi' => $req->harga_pagi,
                'harga_malam' => $req->harga_malam,
                'harga_per_jam' => $req->harga_pagi ?? $req->harga_per_jam, // fallback kompatibilitas
                'diskon_persen' => $req->diskon_persen ?? 0,
                'deskripsi' => $req->deskripsi,
            ]);

            if ($res->successful()) {
                return back()->with('success', 'Lapangan berhasil ditambah');
            }

            $errorMessage = $res->json()['message'] ?? 'Gagal menambah lapangan dari API.';
            return back()->with('error', $errorMessage)->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan server saat menambah lapangan.')->withInput();
        }
    }

    // UPDATE
    public function update(Request $req, $id)
    {
        $req->validate([
            'nama_lapangan' => 'required|string',
        ]);

        $inputNama = strtolower(trim($req->nama_lapangan));
        $existingLapangan = $this->getLapanganList();

        $isDuplicate = collect($existingLapangan)->contains(function ($item) use ($inputNama, $id) {
            $itemId = $item['id_lapangan'] ?? $item['id'] ?? null;
            return strtolower(trim($item['nama_lapangan'] ?? '')) === $inputNama && (string) $itemId !== (string) $id;
        });

        if ($isDuplicate) {
            return back()->with('error', 'Nama lapangan "' . $req->nama_lapangan . '" sudah digunakan oleh lapangan lain!')->withInput();
        }

        try {
            $res = $this->api()->patch(env('API_URL') . "/api/lapangan/$id", [
                'nama_lapangan' => $req->nama_lapangan,
                'harga_pagi' => $req->harga_pagi,
                'harga_malam' => $req->harga_malam,
                'harga_per_jam' => $req->harga_pagi ?? $req->harga_per_jam,
                'diskon_persen' => $req->diskon_persen ?? 0,
                'deskripsi' => $req->deskripsi,
            ]);

            if ($res->successful()) {
                return back()->with('success', 'Lapangan berhasil diupdate');
            }

            $errorMessage = $res->json()['message'] ?? 'Gagal memperbarui lapangan.';
            return back()->with('error', $errorMessage);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan server saat memperbarui lapangan.');
        }
    }

    // DELETE
    public function destroy($id)
    {
        try {
            $res = $this->api()->delete(env('API_URL') . "/api/lapangan/$id");

            if ($res->successful()) {
                return back()->with('success', 'Lapangan berhasil dihapus');
            }

            return back()->with('error', 'Gagal menghapus lapangan dari server.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan server saat menghapus lapangan.');
        }
    }
}