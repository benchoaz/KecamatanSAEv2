<?php

namespace App\Http\Controllers;

use App\Models\WorkDirectory;
use Illuminate\Http\Request;

class WorkDirectoryController extends Controller
{
    /**
     * Display listing of work directory
     */
    public function index(Request $request)
    {
        $query = WorkDirectory::public();

        // Filter by category if provided
        if ($request->filled('kategori')) {
            $query->where('job_category', $request->kategori);
        }

        // Filter by type if provided
        if ($request->filled('tipe')) {
            $query->where('job_type', $request->tipe);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%")
                    ->orWhere('service_area', 'like', "%{$search}%");
            });
        }

        $workItems = $query->latest()->paginate(12);

        // Get categories for filter
        $categories = WorkDirectory::public()
            ->select('job_category')
            ->distinct()
            ->pluck('job_category');

        // Fetch UMKM Data for the "Etalase" tab
        $umkms = \App\Models\UmkmLocal::where('is_active', true)->latest()->limit(8)->get();

        return view('kerja.index', compact('workItems', 'categories', 'umkms'));
    }

    /**
     * Display single work directory item
     */
    public function show($id)
    {
        $workItem = WorkDirectory::public()->findOrFail($id);

        // Get related items (same category, exclude current)
        $relatedItems = WorkDirectory::public()
            ->where('job_category', $workItem->job_category)
            ->where('id', '!=', $workItem->id)
            ->limit(3)
            ->get();

        return view('kerja.show', compact('workItem', 'relatedItems'));
    }
}
