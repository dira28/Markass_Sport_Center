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

    // GET LIST
    public function index()
    {
        try {
            $res = $this->api()->get(env('API_URL') . '/api/lapangan');
            $data = $res->json();

            $lapangan = $data['data'] ?? [];

        } catch (\Exception $e) {
            $lapangan = [];
        }

        return view('admin.pages.lapangan', compact('lapangan'));
    }

    // CREATE
    public function store(Request $req)
    {
        $this->api()->post(env('API_URL') . '/api/lapangan', [
            'nama_lapangan' => $req->nama_lapangan,
            'harga_per_jam' => $req->harga_per_jam,
            'diskon_persen' => $req->diskon_persen,
            'deskripsi' => $req->deskripsi,
        ]);

        return back()->with('success', 'Lapangan berhasil ditambah');
    }

    // UPDATE
    public function update(Request $req, $id)
    {
        $this->api()->patch(env('API_URL') . "/api/lapangan/$id", [
            'nama_lapangan' => $req->nama_lapangan,
            'harga_per_jam' => $req->harga_per_jam,
            'diskon_persen' => $req->diskon_persen,
            'deskripsi' => $req->deskripsi,
        ]);

        return back()->with('success', 'Lapangan berhasil diupdate');
    }

    // DELETE
    public function destroy($id)
    {
        $this->api()->delete(env('API_URL') . "/api/lapangan/$id");

        return back()->with('success', 'Lapangan berhasil dihapus');
    }
}