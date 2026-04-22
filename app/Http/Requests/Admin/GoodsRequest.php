<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\BaseRequest;
use Closure;

class GoodsRequest extends BaseRequest
{
    /**
     * 授权验证
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 验证规则（全数组形式，无字符串拼接）
     */
    public function rules()
    {
        // 获取当前商品ID（编辑场景）
        $goodsId = $this->route('good')?->id;

        // 按业务模块拆分规则数组
        return array_merge(
            $this->basicRules($goodsId),
            [
                'components' => $this->componentRules(),
                'colors' => $this->colorRules($goodsId),
            ]
        );
    }

    /**
     * 基础信息验证规则（全数组形式）
     */
    private function basicRules($goodsId): array
    {
        return [
            'department_id'  => ['required', 'integer'],
            'customer_id'    => ['required', 'integer'],
            'supplier_id'    => ['required', 'integer'],
            'season_id'      => ['required', 'integer'],
            'name'           => ['required', 'string'],
            'customer_sku'   => ['required', 'string' ,'unique:goods,customer_sku,'.$goodsId],
            'brand_logo'     => ['required', 'string'],
            'category_id'    => ['required', 'integer'],
            'warehouse_id'   => ['required', 'integer'],
            'remark'         => ['nullable', 'string'],
            'main_image'     => ['required', 'string'], // 主图路径
            'thumb_image'    => ['required', 'string'], // 缩略图路径
        ];
    }

    /**
     * 成分信息验证规则（全数组形式）
     */
    private function componentRules(): array
    {
        return [
            ['required', 'array', 'min:1'],
            function ($attribute, $value, $fail){
                $components = array_column($value, 'component_id');
                $uniqueComponents = array_unique($components);
                if( count($components) !== count($uniqueComponents) ){
                    $fail("提交数据成分有重复");
                }
                $percents = array_column($value, 'percent');
                $totalPercent = array_sum($percents);
                if( $totalPercent != 100 ){
                    $fail("成分百分比之和为100，请检查数据");
                }
            },
            '*' => [
                'component_id' => ['required', 'integer'],
                'percent'      => ['required', 'numeric', 'between:0,100'],
            ],
        ];
    }

    /**
     * 颜色SKU验证规则（全数组形式 + 颜色唯一性校验）
     */
    private function colorRules($goodsId): array
    {
        return [
            ['required', 'array', 'min:1'],
            //判断提交的数据颜色中有没有重复的
            function ($attribute, $value, $fail) {
                // 提取所有color值并去重
                $colorNames = array_column($value, 'color');
                $uniqueColorNames = array_unique($colorNames);

                // 若去重后数量≠原数量，说明有重复
                if (count($colorNames) !== count($uniqueColorNames)) {
                    // 找出重复的color值
                    $duplicates = array_diff_assoc($colorNames, $uniqueColorNames);
                    $duplicateColor = reset($duplicates); // 取第一个重复的颜色
                    $fail("提交的颜色中包含重复的「{$duplicateColor}」，请删除重复项");
                }
            },
            '*' => [
                'status'                => ['required', 'in:0,1'],
                'color'                 => ['required', 'string',

                    // 自定义规则：同一商品下color唯一，编辑时排除自身ID
//                    function ($attribute, $value, $fail) use ($goodsId) {
//                        // 解析数组索引：colors.0.color → index=0
//                        $index = explode('.', $attribute)[1];
//                        // 获取当前颜色SKU的ID（编辑时前端需传colors.*.id）
//                        $colorId = $this->input("colors.{$index}.id") ?? null;
//
//                        // 校验逻辑：goods_id + color 唯一，排除自身
//                        $exists = \DB::table('goods_color')
//                            ->when($goodsId, function ($query) use ($goodsId) {
//                                $query->where('goods_id', $goodsId);
//                            })
//                            ->where('color', $value)
//                            ->when($colorId, function ($query) use ($colorId) {
//                                $query->where('id', '!=', $colorId);
//                            })
//                            ->exists();
//
//                        if ($exists) {
//                            $fail("当前商品下已存在颜色「{$value}」，请更换");
//                        }
//                    }
                ],
                'color_card'            => ['nullable', 'string'],
                'stock'                 => ['required', 'numeric', 'min:0'],
                'sell_price'            => ['required', 'numeric', 'min:0'],
                'sell_price2'           => ['nullable', 'numeric', 'min:0'],
                'cost_price'            => ['required', 'numeric', 'min:0'],
                'cost_price2'           => ['nullable', 'numeric', 'min:0'],
                'process_price'         => ['nullable', 'numeric', 'min:0'],
                'process_step2_price'   => ['nullable', 'numeric', 'min:0'],
                'sell_currency_id'      => ['required', 'integer', 'min:1'],
                'cost_currency_id'      => ['required', 'integer', 'min:1'],
            ],
        ];
    }

    public function messages()
    {
        return [
            // 基础信息提示
            'department_id.required'        => '请选择部门',
            'customer_id.required'          => '请选择客户',
            'supplier_id.required'          => '请选择供应商',
            'season_id.required'            => '请选择年份季节',
            'name.required'                 => '请填写产品名称',
            'customer_sku.required'         => '请填写产品货号',
            'brand_logo.required'           => '请填写品牌LOGO',
            'category_id.required'          => '请选择类目',
            'warehouse_id.required'         => '请选择仓库',
            'main_image.required'           => '请上传产品主图',
            'thumb_image.required'          => '请上传产品缩略图',

            // 成分信息提示
            'components.required'                 => '至少添加一个成分',
            'components.min'                      => '至少添加一个成分',
            'components.*.component_id.required'  => '请选择成分',
            'components.*.percent.required'       => '请填写成分百分比',
            'components.*.percent.between'        => '成分百分比必须在0-100之间',

            // 颜色SKU提示
            'colors.required'                     => '至少添加一个颜色',
            'colors.min'                          => '至少添加一个颜色',
            'colors.*.status.required'            => '请选择颜色状态',
            'colors.*.status.in'                  => '颜色状态错误',
            'colors.*.color.required'             => '请填写颜色名称',
            'colors.*.stock.min'                  => '库存不能小于0',
            'colors.*.sell_price.min'             => '售价1不能小于0',
            'colors.*.cost_price.min'             => '进价1不能小于0',
            'colors.*.sell_currency_id.required'  => '请选择售价货币类型',
            'colors.*.sell_currency_id.integer'   => '售价货币类型选择错误',
            'colors.*.cost_currency_id.required'  => '请选择进价货币类型',
            'colors.*.cost_currency_id.integer'   => '进价货币类型选择错误',
        ];
    }
}
