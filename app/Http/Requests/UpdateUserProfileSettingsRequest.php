<?php

namespace App\Http\Requests;

use App\Rules\AgeValidationRule;
use App\Rules\MaxLengthMarkdown;
use App\Rules\PhoneValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateUserProfileSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'name' => 'required|max:255',
            'username' => 'required|string|alpha_dash|max:255|unique:users,username,'.Auth::user()->id,
            'email' => 'required|unique:users,email,'.Auth::user()->id,
            'location' => 'max:500',
            'birthdate' => ['required', 'date', new AgeValidationRule],
            'city' => 'required',
            'country' => 'required',
            'postcode' => 'required',
            'phone' => ['required', 'min:10', new PhoneValidationRule]
        ];

        if(getSetting('profiles.max_profile_bio_length') && getSetting('profiles.max_profile_bio_length') !== 0){

            if(getSetting('profiles.allow_profile_bio_markdown')){
                $rules['bio'] = [new MaxLengthMarkdown];
            }
            else{
                $rules['bio'] = 'max:' . getSetting('profiles.max_profile_bio_length');
            }
        }

        return $rules;
    }
}
