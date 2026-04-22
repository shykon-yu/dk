<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    /**
     * 权限验证
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            'department_id'    => ['required', 'integer'],
            'name'             => ['required', 'string', 'max:100'],
            'sort'           => ['required', 'integer'],
            'status'           => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * 自定义提示信息
     */
    public function messages()
    {
        return [
            'department_id.required' => '所属部门不能为空',
            'department_id.integer'   => '所属部门必须为数字',

            'name.required'          => '仓库名称不能为空',
            'name.string'            => '仓库名称必须是字符串',
            'name.max'               => '仓库名称长度不能超过100个字符',

            'sort.required'          => '排序不能为空',
            'sort.integer'           => '排序必须为整数',

            'status.required'        => '状态不能为空',
            'status.integer'         => '状态格式不正确',
            'status.in'              => '状态只能是启用或禁用',
        ];
    }
}
