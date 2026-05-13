<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $orderId = $this->route('order')?->id;
        return [
            'department_id' => ['required', 'integer', 'min:1'],
            'customer_id' => ['required', 'integer', 'min:1'],
            'supplier_id' => ['required', 'integer', 'min:1'],
            'ordered_at' => ['required', 'date'],
            'delivery_at' => ['required', 'date', 'after_or_equal:order_date'],
            'order_code' => ['required', 'string','unique:orders,order_code,'.$orderId],
            'excel_id' => ['nullable', 'integer'],
            'comment' => ['nullable', 'string'],

            'goods' => ['required', 'array', 'min:1'],
            'goods.*.id' => ['nullable', 'integer', 'min:1'],
            'goods.*.goods_id' => ['required', 'integer', 'min:1'],
            'goods.*.sku_id' => ['required', 'integer', 'min:1'],
            'goods.*.color_card' => ['nullable', 'string'],
            'goods.*.number' => ['required', 'numeric', 'min:0.01'],
            'goods.*.currency_id' => ['required', 'integer', 'min:1'],
            'goods.*.price' => ['required', 'numeric', 'min:0.01'],
            'goods.*.money' => ['required', 'numeric', 'min:0.01'],
            'goods.*.remark' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'department_id.required' => '请选择部门',
            'department_id.integer' => '部门ID格式错误',

            'customer_id.required' => '请选择客户',
            'customer_id.integer' => '客户ID格式错误',

            'supplier_id.required' => '请选择供应商',
            'supplier_id.integer' => '供应商ID格式错误',

            'ordered_at.required' => '请选择下单日期',
            'ordered_at.date' => '下单日期格式不正确',

            'delivery_at.required' => '请选择交货日期',
            'delivery_at.date' => '交货日期格式不正确',
            'delivery_at.after_or_equal' => '交货日期不能早于下单日期',

            'order_code.required' => '订单编号不能为空',
            'order_code.unique'   => '订单编号已存在，请勿重复提交',

            'goods.required' => '至少需要添加一个商品',
            'goods.array' => '商品数据格式不正确',
            'goods.min' => '至少需要添加一个商品',

            'goods.*.goods_id.required' => '商品ID不能为空',
            'goods.*.sku_id.required' => 'SKU ID不能为空',
            'goods.*.number.required' => '数量不能为空',
            'goods.*.number.numeric' => '数量必须是数字',
            'goods.*.number.min' => '数量必须大于0',

            'goods.*.currency_id.required' => '请选择货币类型',
            'goods.*.price.required' => '单价不能为空',
            'goods.*.price.numeric' => '单价必须是数字',
            'goods.*.money.required' => '金额不能为空',
            'goods.*.money.numeric' => '金额必须是数字',
        ];
    }
}
