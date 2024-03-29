<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\{
    IsLive,
    LatestFirst,
    ForUser,
    EagerLoad
};

class DesignController extends Controller
{
    protected $designs;

    public function __construct(IDesign $designs)
    {
        // we are injecting the Design/contract class in our controller
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new ForUser(2),
            new EagerLoad(['user', 'comments'])
        ])->all();
        return DesignResource::collection($designs);
    }

    public function findDesign($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    // pick the request that the user is sending through
    public function update(Request $request, $id)
    {
        $design = $this->designs->find($id);

        // apply the DesignPolic, here we authorize the update method and check for the $design resource
        $this->authorize('update', $design);
        // $id because we want to be able to exclude the current design from the validation process
        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
            'team' => ['required_if:assign_to_team,true']
        ]);


        $design = $this->designs->update($id, [
            'team_id' => $request->team,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ]);

        // apply the tags, see eloquent-taggable for more infos
        $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->designs->find($id);
        $this->authorize('delete', $design);
        // delete the files associated with the record (3 files)
        foreach (['thumbnail', 'large', 'original'] as $size) {
            // check if the file exists in the database; call the exists() method which needs the filepath where we want to check if the file exits
            // if that file exits in that storage, go ahead and delete it
            if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
            }
        }
        $this->designs->delete($id);
        return response()->json(['message' => 'Record deleted'], 200);
    }

    public function like($id)
    {
        $total = $this->designs->like($id);
        return response()->json([
            'message' => 'Successful',
            'total' => $total
        ], 200);
    }


    public function checkIfUserHasLiked($designId)
    {
        // check if that user has already liked that design, if they have not, we can show the like button
        $isLiked = $this->designs->isLikedByUser($designId);
        return response()->json(['liked' => $isLiked], 200);
    }

    public function search(Request $request)
    {
        $designs = $this->designs->search($request);
        return DesignResource::collection($designs);
    }

    public function findBySlug($slug)
    {
        $design = $this->designs->withCriteria([
            new IsLive(),
            new EagerLoad(['user', 'comments'])
        ])->findWhereFirst('slug', $slug);
        return new DesignResource($design);
    }

    public function getForTeam($teamId)
    {
        $designs = $this->designs
            ->withCriteria([new IsLive()])
            ->findWhere('team_id', $teamId);
        return DesignResource::collection($designs);
    }

    public function getForUser($userId)
    {
        $designs = $this->designs
            //->withCriteria([new IsLive()])
            ->findWhere('user_id', $userId);
        return DesignResource::collection($designs);
    }

    public function userOwnsDesign($id)
    {
        $design = $this->designs->withCriteria(
            [new ForUser(auth()->id())]
        )->findWhereFirst('id', $id);

        return new DesignResource($design);
    }
}
