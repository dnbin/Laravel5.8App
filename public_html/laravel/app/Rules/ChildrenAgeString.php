<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ChildrenAgeString implements Rule
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
        //
        $ages=explode(',',trim($value));
        foreach($ages as $age){
            if(!ctype_digit(trim($age))){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be comma separated list of children age.';
    }
}
