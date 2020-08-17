<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;

class MatchOldPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // if the validation passed -> returns true. If it doesent pass -> false
        // Hash is a base class ich laravel; we use it to unhash the current password from the database because we only store hashed passwords
        // we take in the value that the user has passed through and we check it against the current authenticated users password
        // returns true if both values are the same
        return Hash::check($value, auth()->user()->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You have provided a wrong current password';
    }
}
