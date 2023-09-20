<?php

namespace App\Http\Requests\Integrations\Hubspot;

use App\VO\Http\StatusCodeVO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactDealRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'dealname' => 'required',
            'dealstage' => 'required',
            'pipeline' => 'required',
        ];
    }

    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $error = [];
        foreach ($validator->errors()->toArray() as $key => $value) {
            $label = trans("text.hubspot.$key");
            $error[$label] = implode(', ', $value);
        }
        throw new HttpResponseException(response()->json([
            'errors' => $error,
            'status' => true
        ], StatusCodeVO::HTTP_UNPROCESSABLE_ENTITY));
    }
}
