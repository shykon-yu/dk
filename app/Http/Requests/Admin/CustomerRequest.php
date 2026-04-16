<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name_kr'          => ['nullable', 'string', 'max:100'],
            'brand_logo'       => ['nullable', 'string', 'max:255'],
            'sku_prefix'       => ['nullable', 'string', 'max:20'],
            'clearance_id'     => ['required', 'integer'],
            'payment_id'       => ['required', 'integer'],
            'contact'          => ['nullable', 'string', 'max:50'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:100'],
            'address'          => ['nullable', 'string'],
            'parent_id'        => ['nullable', 'integer'],
            'status'           => ['required', 'integer', 'in:0,1'],
        ];
    }

    /**
     * 自定义提示信息
     */
    public function messages()
    {
        return [
            'department_id.required'   => '所属部门不能为空',
            'department_id.integer'    => '部门ID格式不正确',

            'name.required'           => '客户名称不能为空',
            'name.string'             => '客户名称必须是字符串',
            'name.max'                => '客户名称长度不能超过100个字符',

            'name_kr.string'          => '韩文名称必须是字符串',
            'name_kr.max'             => '韩文名称长度不能超过100个字符',

            'sku_prefix.string'       => '货号前缀必须是字符串',
            'sku_prefix.max'          => '货号前缀长度不能超过20个字符',

            'clearance_id.required'   => '清关方式不能为空',
            'clearance_id.integer'    => '清关方式ID格式不正确',

            'payment_id.required'     => '支付方式不能为空',
            'payment_id.integer'      => '支付方式ID格式不正确',

            'email.email'             => '邮箱格式不正确',

            'parent_id.integer'       => '父客户ID格式不正确',

            'status.required'         => '状态不能为空',
            'status.in'               => '状态只能选择启用或禁用',
        ];
    }
}
