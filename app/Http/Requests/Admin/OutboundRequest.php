<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OutboundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $outboundId = $this->route('outbound')?->id;

        return [
            'department_id'        => ['required', 'integer', 'min:1'],
            'customer_id'          => ['required', 'integer', 'min:1'],
            'clearance_id'        => ['required', 'integer', 'min:1'],
            'payment_id'          => ['required', 'integer', 'min:1'],
            'tape'                => ['required', 'string'],
            'seal_container_no'   => ['nullable', 'string'],
            'outbound_at'         => ['required', 'date'],
            'outbound_code'       => ['required', 'string', 'unique:outbounds,outbound_code,'.$outboundId],
            'comment'             => ['nullable', 'string'],

            'goods'                      => ['required', 'array', 'min:1'],
            'goods.*.id' => ['nullable', 'integer', 'min:1'],
            'goods.*.brand_logo'         => ['nullable', 'string'],
            'goods.*.warehouse_id'       => ['required', 'integer', 'min:1'],
            'goods.*.goods_id'           => ['required', 'integer', 'min:1'],
            'goods.*.sku_id'             => ['required', 'integer', 'min:1'],
            'goods.*.shipping_mark'      => ['required', 'string'],
            'goods.*.carton_no_start'    => ['required', 'integer'],
            'goods.*.carton_no_end'      => ['required', 'integer'],
            'goods.*.carton_qty'         => ['required', 'numeric', 'min:1'],
            'goods.*.unit_carton_qty'    => ['required', 'numeric', 'min:1'],
            'goods.*.quantity'           => ['required', 'numeric', 'min:0.01'],
            'goods.*.currency_id'        => ['required', 'integer', 'min:1'],
            'goods.*.price'              => ['required', 'numeric', 'min:0.01'],
            'goods.*.carton_length'      => ['required', 'numeric', 'min:0.01'],
            'goods.*.carton_width'       => ['required', 'numeric', 'min:0.01'],
            'goods.*.carton_height'      => ['required', 'numeric', 'min:0.01'],
            'goods.*.craft_method_id'    => ['required', 'integer', 'min:1'],
            'goods.*.gross_weight'       => ['required', 'numeric', 'min:0.01'],
            'goods.*.net_weight'         => ['required', 'numeric', 'min:0.01'],
            'goods.*.remark'             => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'department_id.required' => '请选择部门',
            'department_id.integer'  => '部门格式不正确',
            'department_id.min'      => '请选择有效的部门',

            'customer_id.required' => '请选择客户',
            'customer_id.integer'  => '客户格式不正确',
            'customer_id.min'      => '请选择有效的客户',

            'clearance_id.required' => '请选择清关方式',
            'clearance_id.integer'  => '清关方式格式不正确',
            'clearance_id.min'      => '请选择有效的清关方式',

            'payment_id.required' => '请选择支付方式',
            'payment_id.integer'  => '支付方式格式不正确',
            'payment_id.min'      => '请选择有效的支付方式',

            'tape.required' => '请输入胶带',
            'tape.string'   => '胶带格式不正确',

            'seal_container_no.string' => '封箱号格式不正确',

            'outbound_at.required' => '请选择出库日期',
            'outbound_at.date'     => '出库日期格式不正确',

            'outbound_code.required' => '出库单号不能为空',
            'outbound_code.string'   => '出库单号格式不正确',
            'outbound_code.unique'   => '出库单号已存在，请重新生成',

            'comment.string' => '备注格式不正确',

            'goods.required' => '至少添加一个产品',
            'goods.array'    => '产品数据格式不正确',
            'goods.min'      => '至少添加一个产品',

            'goods.*.brand_logo.string' => '品牌格式不正确',

            'goods.*.warehouse_id.required' => '请选择仓库',
            'goods.*.warehouse_id.integer'  => '仓库格式不正确',
            'goods.*.warehouse_id.min'      => '请选择有效的仓库',

            'goods.*.goods_id.required' => '请选择产品',
            'goods.*.goods_id.integer'  => '产品格式不正确',
            'goods.*.goods_id.min'      => '请选择有效的产品',

            'goods.*.sku_id.required' => '请选择颜色/规格',
            'goods.*.sku_id.integer'  => '颜色/规格格式不正确',
            'goods.*.sku_id.min'      => '请选择有效的颜色/规格',

            'goods.*.shipping_mark.required' => '唛头不能为空',
            'goods.*.shipping_mark.string'   => '唛头格式不正确',

            'goods.*.carton_no_start.required' => '起始箱号不能为空',
            'goods.*.carton_no_start.integer'  => '起始箱号必须是整数数字',

            'goods.*.carton_no_end.required' => '截止箱号不能为空',
            'goods.*.carton_no_end.integer'  => '截止箱号必须是整数数字',

            'goods.*.carton_qty.required' => '箱数不能为空',
            'goods.*.carton_qty.numeric'  => '箱数必须是数字',
            'goods.*.carton_qty.min'      => '箱数必须大于0',

            'goods.*.unit_carton_qty.required' => '单箱数量不能为空',
            'goods.*.unit_carton_qty.numeric'  => '单箱数量必须是数字',
            'goods.*.unit_carton_qty.min'      => '单箱数量必须大于0',

            'goods.*.quantity.required' => '出库数量不能为空',
            'goods.*.quantity.numeric'  => '出库数量必须是数字',
            'goods.*.quantity.min'      => '出库数量必须大于0',

            'goods.*.currency_id.required' => '请选择货币',
            'goods.*.currency_id.integer'  => '货币格式不正确',
            'goods.*.currency_id.min'      => '请选择有效的货币',

            'goods.*.price.required' => '单价不能为空',
            'goods.*.price.numeric'  => '单价必须是数字',
            'goods.*.price.min'      => '单价必须大于0',

            'goods.*.carton_length.required' => '长不能为空',
            'goods.*.carton_length.numeric'  => '长必须是数字',
            'goods.*.carton_length.min'      => '长必须大于0',

            'goods.*.carton_width.required' => '宽不能为空',
            'goods.*.carton_width.numeric'  => '宽必须是数字',
            'goods.*.carton_width.min'      => '宽必须大于0',

            'goods.*.carton_height.required' => '高不能为空',
            'goods.*.carton_height.numeric'  => '高必须是数字',
            'goods.*.carton_height.min'      => '高必须大于0',

            'goods.*.craft_method_id.required' => '请选择工序',
            'goods.*.craft_method_id.integer'  => '工序格式不正确',
            'goods.*.craft_method_id.min'      => '请选择有效的工序',

            'goods.*.gross_weight.required' => '毛重不能为空',
            'goods.*.gross_weight.numeric'  => '毛重必须是数字',
            'goods.*.gross_weight.min'      => '毛重必须大于0',

            'goods.*.net_weight.required' => '净重不能为空',
            'goods.*.net_weight.numeric'  => '净重必须是数字',
            'goods.*.net_weight.min'      => '净重必须大于0',

            'goods.*.remark.string' => '产品备注格式不正确',
        ];
    }
}
