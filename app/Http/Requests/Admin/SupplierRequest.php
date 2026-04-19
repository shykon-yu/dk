<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    /**
     * 确定用户是否有权限进行此请求。
     */
    public function authorize(): bool
    {
        // 后台管理员均有权限操作
        return true;
    }

    /**
     * 获取适用于请求的验证规则。
     * 区分新增/编辑场景，编辑时忽略自身唯一性验证
     */
    public function rules(): array
    {
        $id = $this->route('supplier')?->id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('suppliers')->ignore($id),
            ],
            'contact' => ['nullable', 'string', 'max:50',],
            'phone' => ['nullable', 'string', 'max:20',],
            'email' => ['nullable', 'email', 'max:100',],
            'address' => ['nullable', 'string', 'max:255',],
            'sort' => ['required', 'integer', 'min:0',],
            'status' => ['required', 'integer','in:0,1',],
            'remark' => ['nullable', 'text', 'max:500',],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '供应商名称不能为空',
            'name.string' => '供应商名称必须是字符串',
            'name.max' => '供应商名称不能超过100个字符',
            'name.unique' => '该供应商名称已存在，请更换',

            'contact.string' => '联系人必须是字符串',
            'contact.max' => '联系人不能超过50个字符',

            'phone.string' => '电话必须是字符串',
            'phone.max' => '电话不能超过20个字符',

            'email.email' => '请输入正确的邮箱格式',
            'email.max' => '邮箱不能超过100个字符',

            'address.string' => '地址必须是字符串',
            'address.max' => '地址不能超过255个字符',

            'sort.required' => '排序不能为空',
            'sort.integer' => '排序必须是整数',
            'sort.min' => '排序不能为负数',

            'status.required' => '状态不能为空',
            'status.integer' => '状态必须是整数',
            'status.in' => '状态只能是启用或禁用',

            'remark.max' => '备注不能超过500个字符',
        ];
    }
}
