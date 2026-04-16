<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GoodsCategoryRequest extends BaseRequest
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

    public function rules()
    {
        $id = $this->route('category')?->id;
        return [
            'name'   => ['required', 'string', 'max:50', 'unique:goods_categories,name,' . $id],
            'parent_id'   => ['required', 'integer'],
            'sort' => ['required', 'integer'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '分类名称不能为空',
            'name.string' => '分类名称必须是字符串',
            'name.max' => '分类名称长度不能超过50个字符',
            'name.unique' => '分类名称已存在，请更换',

            'parent_id.required' => '父级分类不能为空',
            'parent_id.integer' => '父级分类格式不正确',

            //'level.required' => '分类级别不能为空',
            //'level.integer' => '分类级别格式不正确',
            //'level.in' => '分类级别只能是一级或二级',

            'sort.required' => '排序不能为空',
            'sort.integer' => '排序必须是数字',

            'status.required' => '状态不能为空',
            'status.integer' => '状态格式不正确',
            'status.in' => '状态只能是启用或禁用',
        ];
    }
}
