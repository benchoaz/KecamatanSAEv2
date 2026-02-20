<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GeospasialWilayahController extends Controller
{
    public function index()
    {
        $geoPath = public_path('data/geo');
        $files = [];

        if (File::exists($geoPath)) {
            foreach (File::files($geoPath) as $file) {
                $files[] = [
                    'name' => $file->getFilename(),
                    'size' => number_format($file->getSize() / 1024, 2) . ' KB',
                    'last_modified' => date('Y-m-d H:i:s', $file->getMTime())
                ];
            }
        }

        return view('kecamatan.settings.geospasial', compact('files'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'geojson_file' => 'required|file',
            'type' => 'required|in:kecamatan,desa,poi'
        ]);

        $geoPath = public_path('data/geo');
        if (!File::exists($geoPath)) {
            File::makeDirectory($geoPath, 0755, true);
        }

        $filename = 'layer_' . $request->type . '.geojson';
        $request->file('geojson_file')->move($geoPath, $filename);

        return redirect()->back()->with('success', 'File GeoJSON ' . $request->type . ' berhasil diperbarui.');
    }
}
