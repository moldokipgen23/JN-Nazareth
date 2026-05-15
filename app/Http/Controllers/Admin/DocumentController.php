<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, Folder $folder)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $path = $uploadedFile->storeAs(
            "folders/{$folder->id}",
            $originalName,
            'local'
        );

        $document = $folder->files()->create([
            'filename'      => $originalName,
            'original_name' => $originalName,
            'path'          => $path,
            'mime_type'     => $uploadedFile->getMimeType(),
            'size'          => $uploadedFile->getSize(),
            'uploaded_by'   => auth()->id(),
        ]);

        ActivityLogger::log('document_uploaded', $document, "Uploaded document '{$originalName}' to folder: {$folder->name}");

        return redirect()->route('admin.folders.show', $folder)
                         ->with('success', 'File uploaded successfully.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('local')->delete($document->path);

        $name = $document->original_name ?? $document->filename;
        $folderId = $document->folder_id;
        $document->delete();

        ActivityLogger::log('document_deleted', null, "Deleted document: {$name}");

        return redirect()->route('admin.folders.show', $folderId)
                         ->with('success', 'File deleted successfully.');
    }

    public function download(Document $document)
    {
        if (!Storage::disk('local')->exists($document->path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($document->path, $document->original_name ?? $document->filename);
    }
}
