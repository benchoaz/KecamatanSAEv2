<?php

namespace App\Http\Controllers;

use App\Models\PelayananFaq;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Display a listing of FAQs by module.
     */
    public function index(Request $request)
    {
        $module = $request->input('module', 'pelayanan');

        $faqs = PelayananFaq::where('module', $module)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.faq.index', compact('faqs', 'module'));
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'module' => 'required|in:pelayanan,umkm,loker',
            'keywords' => 'required|string',
            'question' => 'required|string',
            'answer' => 'required|string',
            'priority' => 'integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $validated['last_updated_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        PelayananFaq::create($validated);

        return redirect()->back()->with('success', 'FAQ berhasil ditambahkan');
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, PelayananFaq $faq)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'module' => 'required|in:pelayanan,umkm,loker',
            'keywords' => 'required|string',
            'question' => 'required|string',
            'answer' => 'required|string',
            'priority' => 'integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $validated['last_updated_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $faq->update($validated);

        return redirect()->back()->with('success', 'FAQ berhasil diperbarui');
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy(PelayananFaq $faq)
    {
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus');
    }

    /**
     * Toggle FAQ active status.
     */
    public function toggle(PelayananFaq $faq)
    {
        $faq->is_active = !$faq->is_active;
        $faq->last_updated_by = auth()->id();
        $faq->save();

        return redirect()->back()->with(
            'success',
            $faq->is_active ? 'FAQ diaktifkan' : 'FAQ dinonaktifkan'
        );
    }
}