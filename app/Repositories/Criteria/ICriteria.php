<?php 

namespace App\Repositories\Criteria;

interface ICriteria
{
    // accepts a list ar array of criteria
    public function withCriteria(...$criteria);

}