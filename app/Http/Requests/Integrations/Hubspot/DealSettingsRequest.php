<?php

namespace App\Http\Requests\Integrations\Hubspot;

use App\VO\Integrations\Hubspot\DealVO;
use Illuminate\Foundation\Http\FormRequest;

class DealSettingsRequest extends FormRequest
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
            //
        ];
    }

    /**
     * Собирает из Request все, выбранные пользователем поля сделки в один массив
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $attributes = (new DealVO())->getOptionalAttributes();
        $deal_settings = [];
        foreach ($attributes as $attribute) {
            if (($this->$attribute ?? '') === 'true') {
                $deal_settings[] = $attribute;
            }
        }
        $this->merge(compact('deal_settings'));
    }
}
