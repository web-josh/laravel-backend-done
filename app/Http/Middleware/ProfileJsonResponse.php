<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;

class ProfileJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // capture the response that we are sending back to the user
        // we basically just store what we return into a variable
        $response = $next($request);

        // check if debugbar is enabled
        // check if its bound to the app container or its bound to the container but not enabled; if yes, just return the $response
        if(! app()->bound('debugbar') || ! app('debugbar')->isEnabled() ){
            return $response;
        }

        // profile the json response
        // check if the response we are getting is a json response (could also be a web response) and if thats the case I dont want to include any debug information->
        // I just want the default information that comes with debugbar
        // also check if we pass the _dubug parameter with the query
        if($response instanceof JsonResponse && $request->has('_debug')){
            
            //debugbar information should only return queries; we use laravels array only helper method for that
            $response->setData(array_merge([
                '_debugbar' => Arr::only(app('debugbar')->getData(), 'queries')
            ], $response->getData(true)));
            
        }

        return $response;

    }
}
