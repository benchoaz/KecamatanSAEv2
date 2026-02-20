<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show($uuid, $filename)
    {
        // 1. Find Submission 
        // DesaScope is automatically applied here!
        // If user is operator_desa, they can only find submissions from their own desa.
        // If they try to access another desa's uuid, this will return null (404).
        $submission = Submission::where('uuid', $uuid)->firstOrFail();

        // 2. Construct Path
        // Matches the structure in SubmissionWebController: bukti_dukung/{uuid}/{filename}
        $path = 'bukti_dukung/' . $uuid . '/' . $filename;

        // 3. Check Existence
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        // 4. Return File Securely
        return response()->file(storage_path('app/' . $path));
    }
}
