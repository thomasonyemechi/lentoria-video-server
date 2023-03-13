<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\StreamController;

class VideoController extends Controller
{
    function uploadNewLectureVideo(Request $request)
    {
        $val = Validator::make($request->all(), [
            'lecture_id' => 'required|integer|min:1',
            'title' => 'string', 
            'video' => 'required|mimes:mp4',
            'duration' => 'integer'
        ]);
        if($val->fails()){return response(['errors'=>$val->errors()->all()],422);}

        $check = Video::where('lecture_id', $request->lecture_id)->first();

        if($check) {
            $check->update(['status' => 0, 'length' => $request->duration ?? 0]);
            $old_video_path = 'videos/'.$check->video;
            $video = $request->file('video');
            $file_name = $this->win_hash(20).'.'.$video->getClientOriginalExtension();
            $check->update([
                'title' => $request->title,
                'video' => $file_name
            ]);
            move_uploaded_file($video, 'videos/'.$file_name);
            if (File::exists($old_video_path)) { unlink($old_video_path); }
            $check->update(['status' => 1]);
            return response([
                'message' => 'Video has been uploaded sucessfully ',
                'url' => $file_name
            ]);
        }

        $hash = $this->win_hashs(20);
        $video = $request->file('video');
        $file_name = $this->win_hash(20).'.'.$video->getClientOriginalExtension();
        $new_vid = Video::create([
            'lecture_id' => $request->lecture_id,
            'title' => $request->title,
            'video_hash' => $hash,
            'status' => 0,
            'video' => $file_name,
            'length' => $request->duration
        ]);
        move_uploaded_file($video, 'videos/'.$file_name);
        $new_vid->update([
            'status' => 1
        ]);
        return response([
            'message' => 'Video has been uploaded sucessfully ',
            'url' => $file_name
        ]);
    }

    
    function fetchVideo($hash)
    {
        $video = Video::where(['video' => $hash])->first();
        if($video->status == 0) {
            return response([
                'message' => 'Video has not been fully uploaded, Pls wait'
            ], 400);
        }

        $path = 'videos/'.$video->video;
        if (!File::exists($path)) {
            return response([
                'message' => 'Video not found'
            ], 404);
        }

        $stream = new StreamController('videos/'.$hash);
        return response()->stream(function() use ($stream) {
            $stream->start();
        });
    }


}
