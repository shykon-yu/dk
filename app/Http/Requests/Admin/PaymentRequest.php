<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'     => ['required', 'string', 'max:255','unique:payments,name,'.$this->id],
            'name_kr'  => ['nullable', 'string', 'max:255'],
            'sort'     => ['nullable', 'integer', 'min:0'],
            'status'   => ['required', 'in:0,1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'    => '支付方式名称不能为空',
            'name.max'         => '支付方式名称过长',
            'name_kr.max'      => '韩文名称过长',
            'sort.integer'     => '排序必须为数字',
            'sort.min'         => '排序不能小于0',
            'status.required'  => '请选择状态',
            'status.in'        => '状态选项错误',
        ];
    }
}
