<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InboundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $inboundId = $this->route('inbound')?->id;
        return [
            'department_id' => ['required', 'integer', 'min:1'],
            'customer_id' => ['required', 'integer', 'min:1'],
            'supplier_id' => ['required', 'integer', 'min:1'],
            'warehouse_id' => ['required', 'integer', 'min:1'],
            'inbound_at' => ['required', 'date'],
            'inbound_code' => ['required', 'string','unique:orders,order_code,'.$inboundId],
            'comment' => ['nullable', 'string'],

            'goods' => ['required', 'array', 'min:1'],
            'goods.*.id' => ['nullable', 'integer', 'min:1'],
            'goods.*.order_item_id' => ['required', 'integer', 'min:0'],
//            'goods.*.order_code' => ['required', 'string'],
            'goods.*.goods_id' => ['required', 'integer', 'min:1'],
            'goods.*.sku_id' => ['required', 'integer', 'min:1'],
            'goods.*.color_card' => ['nullable', 'string'],
            'goods.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'goods.*.currency_id' => ['required', 'integer', 'min:1'],
            'goods.*.price' => ['required', 'numeric', 'min:0.01'],
            'goods.*.remark' => ['nullable', 'string'],
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

            'supplier_id.required' => '请选择供应商',
            'supplier_id.integer'  => '供应商格式不正确',
            'supplier_id.min'      => '请选择有效的供应商',

            'warehouse_id.required' => '请选择仓库',
            'warehouse_id.integer'  => '仓库格式不正确',
            'warehouse_id.min'      => '请选择有效的仓库',

            'inbound_at.required' => '请选择入库日期',
            'inbound_at.date'     => '入库日期格式不正确',

            'inbound_code.required' => '入库单号不能为空',
            'inbound_code.string'   => '入库单号格式不正确',
            'inbound_code.unique'   => '入库单号已存在，请重新生成',

            'comment.string' => '备注格式不正确',

            'goods.required' => '至少添加一个产品',
            'goods.array'    => '产品数据格式不正确',
            'goods.min'      => '至少添加一个产品',

            'goods.*.order_item_id.required' => '订单明细ID不能为空',
            'goods.*.order_item_id.integer'  => '订单明细ID格式不正确',
            'goods.*.order_item_id.min'      => '订单明细ID不能小于0',

//            'goods.*.order_code.required' => '订单编号不能为空',

            'goods.*.goods_id.required' => '请选择产品',
            'goods.*.goods_id.integer'  => '产品格式不正确',
            'goods.*.goods_id.min'      => '请选择有效的产品',

            'goods.*.sku_id.required' => '请选择颜色/规格',
            'goods.*.sku_id.integer'  => '颜色/规格格式不正确',
            'goods.*.sku_id.min'      => '请选择有效的颜色/规格',

            'goods.*.color_card.string' => '色号格式不正确',

            'goods.*.quantity.required' => '入库数量不能为空',
            'goods.*.quantity.numeric'  => '入库数量必须是数字',
            'goods.*.quantity.min'      => '入库数量必须大于0',

            'goods.*.currency_id.required' => '请选择货币',
            'goods.*.currency_id.integer'  => '货币格式不正确',
            'goods.*.currency_id.min'      => '请选择有效的货币',

            'goods.*.price.required' => '单价不能为空',
            'goods.*.price.numeric'  => '单价必须是数字',
            'goods.*.price.min'      => '单价必须大于0',

            'goods.*.remark.string' => '产品备注格式不正确',
        ];
    }
}
