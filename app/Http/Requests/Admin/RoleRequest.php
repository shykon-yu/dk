<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends BaseRequest
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
            'name' => 'required|string|unique:roles,name,' . $this->id,
            'permissions' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '角色名称不能为空！',
            'name.string'   => '角色名称必须是字符串格式！',
            'name.unique'   => '该角色名称已存在，请更换！',
            'permissions.array' => '权限数据格式不正确！',
        ];
    }
}
