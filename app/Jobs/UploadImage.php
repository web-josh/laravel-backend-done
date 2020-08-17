<?php

namespace App\Jobs;

use Image;
use File;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        // we get a Design we pass through from design controller: dispatch of the job/ UploadImage($dedign)
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // every job in laravel comes with a handle(). This function will run, when the job is dispatched
    public function handle()
    {
        // grab the disk that we want to store the images to; again, we pass the disk property from $design from the upload controller
        $disk = $this->design->disk;
        // make a variable out of the filename because we use it alot
        $filename = $this->design->image;
        // this is the file path to where the file is stored on our system (here its in the temporary location that we created)
        $original_file = storage_path() . '/uploads/original/'. $filename;

        // create the large image and save to tmp disk
        // so far we only have the original file in our temporary location
        // http://image.intervention.io for further information
        try{
            // create the Large Image and save to tmp disk
            Image::make($original_file)
                ->fit(800, 600, function($constraint){
                    $constraint->aspectRatio();
                })
                ->save($large = storage_path('uploads/large/'. $filename));

            // Create the thumbnail image
            Image::make($original_file)
                ->fit(250, 200, function($constraint){
                    $constraint->aspectRatio();
                })
                ->save($thumbnail = storage_path('uploads/thumbnail/'. $filename));
            
            // from here we have 3 seperate files in our temporary location that we need to move to a permanent storage
            // put() simply moves a file form a source location to a destination; use fopen() and set file permissions to read and write
            // if this operation is successful then delete the original file
            // original image
            if(Storage::disk($disk)
                ->put('uploads/designs/original/'.$filename, fopen($original_file, 'r+'))){
                    File::delete($original_file);
                }

            // large images
            if(Storage::disk($disk)
                ->put('uploads/designs/large/'.$filename, fopen($large, 'r+'))){
                    File::delete($large);
                }

            // thumbnail images
            if(Storage::disk($disk)
                ->put('uploads/designs/thumbnail/'.$filename, fopen($thumbnail, 'r+'))){
                    File::delete($thumbnail);
                }
            
            // Update the database record with success flag
            $this->design->update([
                'upload_successful' => true
            ]);

            // takes an exception und just logs it in the log file
            // this is to ensure that our application doesent crash it just silently logs the error
        } catch(\Exception $e){
            \Log::error($e->getMessage());
        }

    }
}
