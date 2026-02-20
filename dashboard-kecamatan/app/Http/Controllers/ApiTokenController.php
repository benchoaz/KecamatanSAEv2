<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized. Only Super Admin can manage API tokens.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the tokens.
     */
    public function index()
    {
        $tokens = ApiToken::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('kecamatan.settings.api-tokens.index', compact('tokens'));
    }

    /**
     * Show the form for creating a new token.
     */
    public function create()
    {
        $abilities = ApiToken::ABILITIES;
        return view('kecamatan.settings.api-tokens.create', compact('abilities'));
    }

    /**
     * Store a newly created token in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'nullable|array',
            'abilities.*' => 'string|in:' . implode(',', array_keys(ApiToken::ABILITIES)),
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Generate plain token (shown only once)
        $plainToken = ApiToken::generateTokenString();
        $hashedToken = ApiToken::hashToken($plainToken);

        $token = ApiToken::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'token' => $hashedToken,
            'abilities' => $validated['abilities'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        // Redirect with plain token (only shown once!)
        return redirect()
            ->route('kecamatan.settings.api-tokens.show', $token)
            ->with('plain_token', $plainToken)
            ->with('success', 'Token created successfully. Copy the token now - it will not be shown again!');
    }

    /**
     * Display the specified token.
     */
    public function show(ApiToken $apiToken)
    {
        $plainToken = session('plain_token');
        return view('kecamatan.settings.api-tokens.show', compact('apiToken', 'plainToken'));
    }

    /**
     * Revoke the specified token.
     */
    public function revoke(ApiToken $apiToken)
    {
        $apiToken->revoke();

        return redirect()
            ->route('kecamatan.settings.api-tokens.index')
            ->with('success', 'Token "' . $apiToken->name . '" has been revoked successfully.');
    }

    /**
     * Remove the specified token from storage.
     */
    public function destroy(ApiToken $apiToken)
    {
        $name = $apiToken->name;
        $apiToken->delete();

        return redirect()
            ->route('kecamatan.settings.api-tokens.index')
            ->with('success', 'Token "' . $name . '" has been deleted permanently.');
    }
}