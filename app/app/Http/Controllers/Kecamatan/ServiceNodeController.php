<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\MasterLayanan;
use App\Models\ServiceNode;
use App\Models\ServiceRequirement;
use App\Services\ServiceTreeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceNodeController extends Controller
{
    public function __construct(private ServiceTreeService $treeService) {}

    /**
     * Halaman Node Manager untuk satu master_layanan
     */
    public function index(int $id)
    {
        $layanan = MasterLayanan::findOrFail($id);

        $nodes = ServiceNode::where('master_layanan_id', $id)
            ->whereNull('parent_id')
            ->with(['allChildren' => function ($q) {
                $q->withCount('requirements')->orderBy('urutan');
            }])
            ->withCount('requirements')
            ->orderBy('urutan')
            ->get();

        return view('kecamatan.pelayanan.layanan.nodes', compact('layanan', 'nodes'));
    }

    /**
     * Simpan node baru
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'master_layanan_id' => 'required|exists:master_layanan,id',
            'parent_id'         => 'nullable|exists:service_nodes,id',
            'depth'             => 'required|integer|min:0',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'ikon'              => 'nullable|string|max:100',
            'urutan'            => 'required|integer|min:0',
            'is_leaf'           => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
        ]);

        $masterLayananId = $request->input('master_layanan_id');

        $validated['master_layanan_id'] = $masterLayananId;
        $validated['is_leaf']   = $request->boolean('is_leaf');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['parent_id'] = $validated['parent_id'] ?: null;

        ServiceNode::create($validated);

        // Aktifkan flag has_nodes pada master_layanan
        MasterLayanan::where('id', $masterLayananId)
            ->update(['has_nodes' => true]);

        $this->treeService->clearCache((int) $masterLayananId);

        return redirect()
            ->route('kecamatan.pelayanan.layanan.nodes.index', $masterLayananId)
            ->with('success', 'Node berhasil ditambahkan!');
    }

    /**
     * Hapus node (cascade ke children & requirements)
     */
    public function destroy(ServiceNode $node): \Illuminate\Http\RedirectResponse
    {
        $layananId = $node->master_layanan_id;
        $node->delete();

        // Matikan flag jika sudah tidak ada node lagi
        $remaining = ServiceNode::where('master_layanan_id', $layananId)->count();
        if ($remaining === 0) {
            MasterLayanan::where('id', $layananId)->update(['has_nodes' => false]);
        }

        $this->treeService->clearCache($layananId);

        return back()->with('success', 'Node berhasil dihapus.');
    }

    /**
     * Simpan requirement baru (via AJAX)
     */
    public function storeRequirement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'node_id'        => 'required|exists:service_nodes,id',
            'type'           => 'required|in:file_upload,text_info,checkbox',
            'label'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_required'    => 'nullable|boolean',
            'accepted_types' => 'nullable|string|max:100',
            'max_size_mb'    => 'nullable|integer|min:1|max:20',
            'urutan'         => 'nullable|integer|min:0',
        ]);

        $validated['is_required'] = $request->boolean('is_required', true);

        ServiceRequirement::create($validated);

        return response()->json(['success' => true]);
    }

    /**
     * API: Ambil requirements untuk satu node (via AJAX)
     */
    public function getRequirements(int $nodeId): JsonResponse
    {
        $requirements = ServiceRequirement::where('node_id', $nodeId)
            ->orderBy('urutan')
            ->get();

        return response()->json(['requirements' => $requirements]);
    }

    /**
     * API: Hapus requirement (via AJAX)
     */
    public function destroyRequirement(int $id): JsonResponse
    {
        ServiceRequirement::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * API: Ambil child nodes (untuk step navigator warga)
     */
    public function getChildren(int $nodeId): JsonResponse
    {
        $children = ServiceNode::where('parent_id', $nodeId)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get(['id', 'name', 'description', 'ikon', 'is_leaf']);

        $node = ServiceNode::select('id', 'name', 'is_leaf', 'parent_id')->find($nodeId);

        return response()->json([
            'node'     => $node,
            'children' => $children,
        ]);
    }
}
