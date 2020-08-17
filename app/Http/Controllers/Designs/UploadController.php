<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IDesign;

class UploadController extends Controller
{
    protected $designs;

    public function __construct(IDesign $designs)
    {
        $this->designs = $designs;
    }

    public function upload(Request $request)
    {
        // validate the request
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]); 

        // get the image from the request
        $image = $request->file('image');
        $image_path = $image->getPathName();


        // get the original file name and replace any spaces with underscores and make it lower cases
        // also get a timestamp in case files with equal filename gets uploaded: timestamp()_lower_case.png
        // time() gives the current timestamp, then append to an underscore, then use php preg_replace function which takes a regular expression
        // and replaces it with an underscore
        $filename = time()."_". preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
        
        // move the image to the temporary location (here tmp)
        // storeAs() comes with laravel's storage facade, it takes the sub folder that we want to save in, second arguement is the name of the file, here we just
        // pass the filename what we generated above
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        // once that file is moved we want to create the database record for the design
        // there is a relationship in the user model which links to this design (here we tap into that relationship)
        $design = $this->designs->create([
            'user_id' => auth()->id(),
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        // dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));
        
        return response()->json($design, 200);

    }
}
