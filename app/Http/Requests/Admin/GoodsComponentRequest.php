<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GoodsComponentRequest extends BaseRequest
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
        $id = $this->route('component') ? $this->route('component')->id : null;
        return [
            'name'      => [
                'required',
                'string',
                'max:50',
                'regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_\s]+$/u',
                'unique:goods_components,name,'. $id
            ],

            'name_en'   => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z\s\-]+$/'
            ],

            'name_kr'   => [
                'required',
                'string',
                'max:50',
                'regex:/^[\x{AC00}-\x{D7AF}]+$/u'
            ],

            'sort'      => ['required', 'integer'],
            'status'    => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '中文名称不能为空',
            'name.string' => '中文名称必须是字符串',
            'name.max' => '中文名称长度不能超过50个字符',
            'name.unique' => '中文名称已存在，请更换',
            'name.regex' => '中文名称只能包含中文、英文、数字、空格和下划线',

            'name_en.required' => '英文名称不能为空',
            'name_en.string' => '英文名称必须是字符串',
            'name_en.max' => '英文名称长度不能超过50个字符',
            'name_en.regex' => '英文名称只能输入英文字母、空格和横杠',

            'name_kr.required' => '韩文名称不能为空',
            'name_kr.string' => '韩文名称必须是字符串',
            'name_kr.max' => '韩文名称长度不能超过50个字符',
            'name_kr.regex' => '韩文名称只能输入韩文',

            'sort.required' => '排序不能为空',
            'sort.integer' => '排序必须是整数',

            'status.required' => '状态不能为空',
            'status.in' => '状态只能是启用或禁用',
        ];
    }
}
