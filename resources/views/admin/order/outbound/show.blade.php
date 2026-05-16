@extends('admin.layouts.app')

@section('extends_css')
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/ajaxupload/css/style.css"/>
    <style>
        #log td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-row {
            display: flex;
            gap: 4px;
            justify-content: center;
            align-items: center;
        }

        .orderCodeFixed {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            padding: 8px 0;
        }
        .modal-body .table {
            margin-top: 10px !important;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">入库详情</span>
        </div>
        <div class="panel-body navbar-form">
            <form id="order" onkeydown="if(event.keyCode==13)return false;">

                <select class="form-control input-sm" name="department_id" id="department_id" disabled>
                    <option value="">-请选择部门-</option>
                    @foreach($_departments_auth as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $inbound->department_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <select class="form-control input-sm" name="customer_id" id="customer_id" disabled>
                    <option value="">-请选择客户-</option>
                    <option value="{{ $inbound->customer_id }}" selected>{{ $inbound->customer->name }}</option>
                </select>

                <select class="form-control input-sm" name="warehouse_id" id="warehouse_id" disabled>
                    <option value="">-请选择仓库-</option>
                    @foreach($warehouses as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $inbound->warehouse_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <select name="supplier_id" id="supplier_id" class="selectpicker"
                        data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true" title="请选择供应商" disabled>
                    @foreach($_suppliers as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $inbound->supplier_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <div class="input-group">
                    <input class="form-control input-sm" name="inbound_at" id="inbound_at" autocomplete="off"
                           placeholder="入库日期" type="text" value="{{ $inbound->inbound_at_date }}" readonly disabled>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <span class="order">入库单号：<span id="inbound_code">{{ $inbound->inbound_code }}</span></span>

                <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped"
                       style="margin-top:2em;">
                    <thead>
                    <tr>
                        <th colspan="2">排头信息</th>
                        <th colspan="4">产品信息</th>
                        <th colspan="6">订单信息</th>
                    </tr>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>订单</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>剩余数量</th>
                        <th>入库数量</th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody class="text-center p_body padding_0" id="log">

                    {{-- 循环回显子单 --}}
                    @foreach($inbound->items as $key => $item)
                        <tr data-skuList="{{ $item->goods->skus }}">
                            <td>
                                查看
                            </td>
                            <td class="serial_number">{{ $key+1 }}</td>
                            <td data-key="inbound_item_id" class="padding_0" hidden>
                                <input type="text" class="form-control input_no_border input-sm inbound_item_id"
                                       name="inbound_item_id"
                                       value="{{ $item->id }}" readonly disabled>
                            </td>
                            <td data-key="order_code" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm order_code" name="order_code"
                                       value="{{ $item->order_item_id == 0 ? '关联订单': $item->orderItem->order->order_code }}" readonly disabled>
                            </td>
                            <td data-key="order_item_id" class="padding_0" hidden>
                                <input type="text" class="form-control input_no_border input-sm order_item_id" name="order_item_id"
                                       value="{{ $item->order_item_id }}" readonly disabled>
                            </td>

                            <td data-key="goods_id" class="padding_0 select_goods">
                                <select name="goods_id"
                                        class="input_no_border form-control selectpicker goods_id"
                                        data-live-search="true"
                                        data-live-search-placeholder="输入货号/名称搜索"
                                        title="请选择产品" disabled>

                                    {{-- 1. 先判断：当前商品不在列表里 → 插到最前面并选中 --}}
                                    @php
                                        $currentGoodsId = $item->goods_id;
                                        $existsInList = $goods->contains('id', $currentGoodsId);
                                    @endphp

                                    {{-- 不在列表里 → 优先显示在顶部 --}}
                                    @if(!$existsInList)
                                        <option value="{{ $item->goods_id }}" selected>
                                            {{ $item->goods->customer_sku ?? '' }} {{ $item->goods->name ?? '' }}
                                        </option>
                                    @endif

                                    {{-- 2. 循环正常200条商品 --}}
                                    @foreach($goods as $good)
                                        <option value="{{ $good->id }}" {{ $good->id == $currentGoodsId ? 'selected' : '' }}>
                                            {{ $good->customer_sku }} {{ $good->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </td>

                            <td>
                                <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; ">
                                    <img height="20px" src="{{ $item->goods->thumb_image ? asset($item->goods->thumb_image) : '' }}"
                                         class="thumb-img click-preview"
                                         data-src="{{ $item->goods->main_image ? asset($item->goods->main_image) : '' }}"
                                         style="width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">
                                    <img src="{{ $item->goods->main_image ? asset($item->goods->main_image) : '' }}"
                                         class="hover-preview"
                                         style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                                </div>
                            </td>

                            <td data-key="sku_id" class="padding_0">
                                <select name="sku_id" class="form-control input_no_border input-sm sku_id" disabled>
                                    <option value="">--请选择颜色--</option>
                                    @foreach($item->goods->skus ?? [] as $sku)
                                        <option value="{{ $sku->id }}" {{ $sku->id == $item->sku_id ? 'selected' : '' }}>
                                            {{ $sku->color ?? '无颜色' }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="padding_0">
                            <span class="number_difference">
                                @if($item->order_item_id != 0)
                                    {{ $item->orderItem->number - $item->orderItem->received_quantity ?? '' }}
                                @endif

                            </span>
                            </td>

                            <td data-key="quantity" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm quantity"
                                       name="quantity" value="{{ $item->quantity }}" placeholder="入库数量" readonly disabled>
                            </td>

                            <td data-key="currency_id" class="padding_0">
                                <select class="form-control input_no_border input-sm currency_id" name="currency_id" disabled>
                                    @foreach($_currencies as $vo)
                                        <option value="{{ $vo->id }}" {{ $vo->id == $item->currency_id ? 'selected' : '' }}>
                                            {{ $vo->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td data-key="price" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm price"
                                       name="price" value="{{ $item->price }}" placeholder="单价" readonly disabled>
                            </td>

                            <td data-key="money" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm money"
                                       name="money" readonly value="{{ $item->amount }}" placeholder="金额" disabled>
                            </td>

                            <td data-key="remark" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm remark"
                                       name="remark" value="{{ $item->remark }}" placeholder="备注" readonly disabled>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>订单</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>订单数量</th>
                        <th>入库数量<br><span id="total_quantity"></span></th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额<br><span id="total_money"></span></th>
                        <th>备注</th>
                    </tr>
                    <tr class="hidden" id="timg">
                        <th colspan="19"><img src="/images/timg.gif"/>正在提交中...</th>
                    </tr>
                    </tfoot>
                </table>

                {{-- 保存按钮已删除 --}}

            </form>
        </div>
    </div>

    <div class="form-group container">
        <label for="comment">其他注意事项:</label>
        <textarea class="form-control" rows="5" id="comment" readonly disabled>{{ $inbound->comment }}</textarea>
    </div>

    <div id="success" class="text-center text-info" style="display:none;">
        <div style="margin-top:1.5em"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;修改成功</div>
    </div>

@endsection

@section('script_js')
    <script>
        var CURRENT_CUSTOMER_DEFAULT_GOODS = @json($goods ?? collect());
        var GOODS_SEARCH_KEYWORD = '';
        var inboundId = {{ $inbound->id }};

        $(function () {
            refreshSerial();
            totalAll();

            function refreshSerial() {
                $(".serial_number").each(function (i) {
                    $(this).text(i + 1);
                });
            }

            // 合计
            function totalAll() {
                total("quantity", 0);
                total("money", 2);
            }

            function total(t, n) {
                let sum = 0;
                $("." + t).each(function () {
                    sum += parseFloat($(this).val() || 0);
                });
                $("#total_" + t).text(sum.toFixed(n));
            }

            // 刷新下拉框（禁用状态）
            $('.selectpicker').selectpicker('refresh');
        });
    </script>
@endsection
