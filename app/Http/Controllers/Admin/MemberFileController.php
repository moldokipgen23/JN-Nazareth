<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberFileController extends Controller
{
    public function store(Request $request, Member $member)
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
            "members/{$member->id}",
            $originalName,
            'local'
        );

        $memberFile = $member->files()->create([
            'filename'    => $originalName,
            'path'        => $path,
            'size'        => $uploadedFile->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLogger::log('member_file_uploaded', $memberFile, "Uploaded '{$originalName}' for member: {$member->name}");

        return redirect()->route('admin.members.show', $member)
                         ->with('success', 'File uploaded successfully.');
    }

    public function destroy(MemberFile $memberFile)
    {
        $member = $memberFile->member;

        Storage::disk('local')->delete($memberFile->path);

        $name = $memberFile->filename;
        $memberFile->delete();

        ActivityLogger::log('member_file_deleted', null, "Deleted file '{$name}' from member: {$member->name}");

        return redirect()->route('admin.members.show', $member)
                         ->with('success', 'File deleted successfully.');
    }

    public function download(MemberFile $memberFile)
    {
        if (!Storage::disk('local')->exists($memberFile->path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($memberFile->path, $memberFile->filename);
    }
}
