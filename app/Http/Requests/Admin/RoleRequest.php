<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|unique:roles,name,' . $this->id,
            'level' => 'required|integer|between:1,100',
            'permissions' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '角色名称不能为空！',
            'name.string'   => '角色名称必须是字符串格式！',
            'name.unique'   => '该角色名称已存在，请更换！',
            'level.required' => '层级数不能为空！',
            'level.integer' => '层级数为整数！',
            'level.between' => '层级数为1-100的数字！',
            'permissions.array' => '权限数据格式不正确！',
        ];
    }
}
