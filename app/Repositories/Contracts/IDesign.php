<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IDesign 
{
    // this php interface holds all the methods that we want to implement in the repository
    // its basically just a list of methods available in the repository
    public function applyTags($id, array $data);
    public function addComment($designId, array $data);
    public function like($id);
    public function isLikedByUser($id);
    public function search(Request $request);
}