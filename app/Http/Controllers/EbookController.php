<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class EbookController extends Controller
{
    function uploadEbook(Request $request)
    {
        $val = Validator::make($request->all(), [
            'course_id' => 'required|integer|min:1',
            'file' => 'required|mimes:pdf',
            'title' => 'required|string'
        ]);
        if ($val->fails()) {
            return response(['errors' => $val->errors()->all()], 422);
        }


        $check = Ebook::where('course_id', $request->course_id)->first();

        if ($check) {
            $old_file_path = 'document/' . $check->file;
            $file = $request->file('file');
            $file_name = $this->win_hashs(20) . '.' . $file->getClientOriginalExtension();
            $check->update([
                'file' => $file_name,
                'title' => $request->title
            ]);
            move_uploaded_file($file, 'document/' . $file_name);
            if (File::exists($old_file_path)) {
                unlink($old_file_path);
            }
            return response([
                'message' => 'Document has been uploaded sucessfully ',
                'url' => $file_name
            ]);
        }

        $hash = $this->win_hashs(20);
        $file = $request->file('file');
        $file_name = $this->win_hashs(20) . '.' . $file->getClientOriginalExtension();
        $new_vid = Ebook::create([
            'course_id' => $request->course_id,
            'file_hash' => $hash,
            'file' => $file_name,
            'title' => $request->title
        ]);
        move_uploaded_file($file, 'document/' . $file_name);
        return response([
            'message' => 'Document has been uploaded sucessfully ',
            'url' => $file_name
        ]);
    }



    function fetchEbook($course_id, $user_id)
    {
        $book = Ebook::where(['course_id' => $course_id])->first();

        $path = 'document/'.$book->file;
        if (!File::exists($path)) {
            return response([
                'message' => 'Ebook was not found'
            ], 404);
        }

        Download::create([
            'file_id' => $book->id,
            'user_id' => $user_id,
        ]);

        return response()->download($path, $book->title);
    }



    function fetchEbookDownloadHistory($course_id)
    {
        $book =  Ebook::where('course_id', $course_id)->first();     
        if(!$book) {
            return response([
                'message' => 'No ebook was found for this course'
            ], 404);
        }
        $downloads = Download::where('file_id', $book->id)->paginate(100);
        return response([
            'book' => $book,
            'downloads' => $downloads
        ], 200);
    }
}
}
