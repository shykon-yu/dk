<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GoodsSeasonRequest extends BaseRequest
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
        return [
            'name'   => ['required', 'string', 'max:50', 'unique:goods_seasons,name,' . $this->id],
            'year'   => ['required', 'integer', 'max:9999'],
            'season' => ['required', 'integer', 'in:1,2'],
            'status' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'   => '季节名称不能为空',
            'name.string'     => '季节名称必须是字符串',
            'name.max'        => '季节名称长度不能超过50个字符',
            'name.unique'     => '季节名称已存在，请更换',

            'year.required'   => '年份不能为空',
            'year.integer'    => '年份必须是整数',
            'year.max'        => '年份不能超过9999',

            'season.required' => '季节类型不能为空',
            'season.integer'  => '季节类型必须是整数',
            'season.in'       => '季节类型只能是 1(春季) 或 2(夏季)',

            'status.required' => '状态不能为空',
            'status.integer'  => '状态必须是整数',
            'status.in'       => '状态只能是 0(禁用) 或 1(启用)',
        ];
    }
}
