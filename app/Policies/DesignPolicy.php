<?php

namespace App\Policies;

use App\Models\Design;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DesignPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any designs.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the design.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Design  $design
     * @return mixed
     */
    public function view(User $user, Design $design)
    {
        //
    }

    /**
     * Determine whether the user can create designs.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the design.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Design  $design
     * @return mixed
     */
    // returns a boolean true or false, true means that the person is authorized
    // with return false; nobody would be able do update a design
    public function update(User $user, Design $design)
    {
        // User is automatically injected by laravel which picks up the currently authenticated user
        return $design->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the design.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Design  $design
     * @return mixed
     */
    public function delete(User $user, Design $design)
    {
        return $design->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the design.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Design  $design
     * @return mixed
     */
    public function restore(User $user, Design $design)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the design.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Design  $design
     * @return mixed
     */
    public function forceDelete(User $user, Design $design)
    {
        //
    }
}
