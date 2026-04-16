<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends BaseRequest
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
        $id = $this->route('department')?->id;

        return [
            'name' => ['required', 'string' , 'max:100' , 'unique:departments,name,'.$id],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '部门名称不能为空',
            'name.string'   => '部门名称必须是字符串',
            'name.max'      => '部门名称长度不能超过100个字符',

            'status.required' => '状态不能为空',
            'status.integer'  => '状态格式不正确',
            'status.in'       => '状态只能选择启用或禁用',
        ];
    }
}
