<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Menu;

class MenuRequest extends BaseRequest
{
    /**
     * 授权验证：后台操作直接允许
     */
    public function authorize()
    {
        return true; // 后台管理无需做权限判断，直接返回true
    }

    /**
     * 定义验证规则（区分新增/编辑场景）
     */
    public function rules()
    {
        // 根据路由/请求判断场景：编辑有id，新增无id
        $isUpdate = $this->has('id');

        return [
            'id'         => $isUpdate ? ['required', 'integer', 'exists:menus,id'] : [],
            'title'      => ['required', 'string', 'max:50'],
            'permission' => ['nullable','string', 'max:100'],
            // 上级菜单：可选+整数+存在于menus表（顶级菜单parent_id=0，单独排除）
            'parent_id'  => ['nullable', 'integer', function ($attribute, $value, $fail) {
                if ($value != 0 && !Menu::where('id', $value)->exists()) {
                    $fail('上级菜单不存在，请选择有效菜单');
                }
            }],
            'route'      => ['nullable', 'string', 'max:100'],
            'sort'       => ['nullable', 'integer', 'min:0'],
            'auto_create_permission' => 'nullable|boolean',
        ];
    }

    /**
     * 自定义验证提示语（中文，更友好）
     */
    public function messages()
    {
        return [
            'id.required'    => '菜单ID不能为空',
            'id.integer'     => '菜单ID必须为整数',
            'id.exists'      => '该菜单不存在',
            'title.required' => '菜单名称不能为空',
            'title.string'   => '菜单名称必须为字符串',
            'title.max'      => '菜单名称不能超过50个字符',
//            'permission.string' => '权限名称必须为字符串',
            'permission.max' => '权限名称不能超过100字符',
            'parent_id.integer' => '上级菜单ID必须为整数',
            'route.max'      => '路由名称不能超过100个字符',
            'sort.integer'   => '排序号必须为整数',
            'sort.min'       => '排序号不能为负数',
        ];
    }
}
