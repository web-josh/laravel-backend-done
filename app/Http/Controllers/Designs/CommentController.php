<?php

namespace App\Http\Controllers\Designs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Contracts\IComment;

class CommentController extends Controller
{
    
    protected $comments;
    protected $designs;

    public function __construct(IComment $comments, IDesign $designs)
    {
        // inject the repository
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $designId)
    {
        $this->validate($request, [
            'body' => ['required']
        ]);
        
        // call the addComment() method in the DesignRepository
        $comment = $this->designs->addComment($designId, [
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $id)
    {
        // search comment by id
        $comment = $this->comments->find($id);
        // check for authorization on the comment
        $this->authorize('update', $comment);

        // validate the request that is coming through, all we need from the ui is the body of the comment
        $this->validate($request, [
            'body' => ['required']
        ]);
        // then simply update the comment (using the repository comments)
        $comment = $this->comments->update($id, [
            'body' => $request->body
        ]);
        return new CommentResource($comment);
    }

    public function destroy($id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('delete', $comment);
        $this->comments->delete($id);
        return response()->json(['message' => 'Item deleted'], 200);
    }

    
}
