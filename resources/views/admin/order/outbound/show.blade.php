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
            <span class="panel-tit">出库详情</span>
        </div>
        <div class="panel-body navbar-form">
            <form id="order" onkeydown="if(event.keyCode==13)return false;">

                <!-- 部门 -->
                <select class="form-control input-sm" name="department_id" id="department_id" disabled>
                    <option value="">-请选择部门-</option>
                    @foreach($_departments_auth as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $outbound->department_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 客户 -->
                <select class="form-control input-sm" name="customer_id" id="customer_id" disabled>
                    <option value="">-请选择客户-</option>
                    <option value="{{ $outbound->customer_id }}" selected>{{ $outbound->customer->name }}</option>
                </select>

                <!-- 清关方式 -->
                <select class="form-control input-sm" name="clearance_id" id="clearance_id" disabled>
                    @foreach($_clearances as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $outbound->clearance_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 支付方式 -->
                <select class="form-control input-sm" name="payment_id" id="payment_id" disabled>
                    @foreach($_payments as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $outbound->payment_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 胶带 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="tape" placeholder="胶带" value="{{ $outbound->tape }}" readonly disabled>
                </div>

                <!-- 封箱号 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="seal_container_no" placeholder="封箱号" value="{{ $outbound->seal_container_no }}" readonly disabled>
                </div>

                <!-- 出库日期 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="outbound_at" id="outbound_at" autocomplete="off"
                           placeholder="出库日期" type="text"
                           value="{{ $outbound->outbound_at ? \Carbon\Carbon::parse($outbound->outbound_at)->toDateString() : '' }}" readonly disabled>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <!-- 出库单号 -->
                <span class="order">出库单号：<span id="outbound_code">{{ $outbound->outbound_code }}</span></span>

                <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped"
                       style="margin-top:2em;">
                    <thead>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>唛头</th>
                        <th>起始</th>
                        <th>截止</th>
                        <th>仓库</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>库存</th>
                        <th>品牌</th>
                        <th>箱数</th>
                        <th>单箱数</th>
                        <th>数量</th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额</th>
                        <th>长</th>
                        <th>宽</th>
                        <th>高</th>
                        <th>CBM</th>
                        <th>工序</th>
                        <th>毛重</th>
                        <th>净重</th>
                        <th>备注</th>
                    </tr>
                    </thead>

                    <tbody class="text-center p_body padding_0" id="log">

                    @foreach($outbound->items as $key => $item)
                        <tr>
                            <td>
                                查看
                            </td>
                            <td class="serial_number">{{ $key+1 }}</td>

                            <!-- 唛头 -->
                            <td data-key="shipping_mark" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm shipping_mark"
                                       name="shipping_mark" value="{{ $item->shipping_mark }}" readonly disabled>
                            </td>

                            <!-- 起始 -->
                            <td data-key="carton_no_start" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_no_start"
                                       name="carton_no_start" value="{{ $item->carton_no_start }}" readonly disabled>
                            </td>

                            <!-- 截止 -->
                            <td data-key="carton_no_end" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_no_end"
                                       name="carton_no_end" value="{{ $item->carton_no_end }}" readonly disabled>
                            </td>

                            <!-- 仓库 -->
                            <td data-key="warehouse_id" class="padding_0">
                                <select class="form-control input_no_border input-sm warehouse_id" name="warehouse_id" disabled>
                                    <option value="">-仓库-</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ $w->id == $item->warehouse_id ? 'selected' : '' }}>
                                            {{ $w->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 产品 -->
                            <td data-key="goods_id" class="padding_0 select_goods">
                                <select name="goods_id"
                                        class="input_no_border form-control selectpicker goods_id"
                                        data-live-search="true"
                                        data-live-search-placeholder="输入货号/名称搜索"
                                        title="请选择产品" disabled>

                                    @foreach($item->available_goods as $good)
                                        <option value="{{ $good->id }}" {{ $good->id == $item->goods_id ? 'selected' : '' }}>
                                            {{ $good->customer_sku }} {{ $good->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 图片 -->
                            <td class="preview_image padding_0" image_url="{{ asset($item->goods->thumb_image ?? '') }}">
                                <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle;">
                                    <img height="20px" src="{{ $item->goods->thumb_image ? asset($item->goods->thumb_image) : '' }}"
                                         class="thumb-img click-preview"
                                         data-src="{{ $item->goods->main_image ? asset($item->goods->main_image) : '' }}">
                                    <img src="{{ $item->goods->main_image ? asset($item->goods->main_image) : '' }}"
                                         class="hover-preview"
                                         style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                                </div>
                            </td>

                            <!-- SKU 颜色 -->
                            <td data-key="sku_id" class="padding_0">
                                <select name="sku_id" class="form-control input_no_border input-sm sku_id" disabled>
                                    <option value="">--请选择颜色--</option>
                                    @foreach($item->available_skus ?? [] as $sku)
                                        <option value="{{ $sku->id }}" {{ $sku->id == $item->sku_id ? 'selected' : '' }}>
                                            {{ $sku->color ?? '无颜色' }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 库存 -->
                            <td data-key="stock" class="padding_0 stock">
                                {{ $item->stock_info->stock ?? '' }}
                            </td>

                            <!-- 品牌 -->
                            <td data-key="brand_logo" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm brand_logo"
                                       name="brand_logo" value="{{ $item->brand_logo }}" readonly disabled>
                            </td>

                            <!-- 箱数 -->
                            <td data-key="carton_qty" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_qty"
                                       name="carton_qty" value="{{ $item->carton_qty }}" readonly disabled>
                            </td>

                            <!-- 单箱数量 -->
                            <td data-key="unit_carton_qty" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm unit_carton_qty"
                                       name="unit_carton_qty" value="{{ $item->unit_carton_qty }}" readonly disabled>
                            </td>

                            <!-- 数量 -->
                            <td data-key="quantity" class="padding_0">
                                <input type="text" size="5" class="form-control input_no_border input-sm quantity"
                                       name="quantity" value="{{ $item->quantity }}" readonly disabled>
                            </td>

                            <!-- 货币 -->
                            <td data-key="currency_id" class="padding_0">
                                <select class="form-control input_no_border input-sm currency_id" name="currency_id" disabled>
                                    @foreach($_currencies as $vo)
                                        <option value="{{ $vo->id }}" {{ $vo->id == $item->currency_id ? 'selected' : '' }}>
                                            {{ $vo->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 单价 -->
                            <td data-key="price" class="padding_0">
                                <input type="text" size="5" class="form-control input_no_border input-sm price"
                                       name="price" value="{{ $item->price }}" readonly disabled>
                            </td>

                            <!-- 金额 -->
                            <td data-key="amount" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm amount"
                                       name="amount" readonly value="{{ $item->amount }}" disabled>
                            </td>

                            <!-- 长 -->
                            <td data-key="carton_length" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_length"
                                       name="carton_length" value="{{ $item->carton_length }}" readonly disabled>
                            </td>

                            <!-- 宽 -->
                            <td data-key="carton_width" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_width"
                                       name="carton_width" value="{{ $item->carton_width }}" readonly disabled>
                            </td>

                            <!-- 高 -->
                            <td data-key="carton_height" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_height"
                                       name="carton_height" value="{{ $item->carton_height }}" readonly disabled>
                            </td>

                            <!-- CBM -->
                            <td data-key="cbm" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm cbm"
                                       name="cbm" readonly value="{{ $item->cbm }}" disabled>
                            </td>

                            <!-- 工序 -->
                            <td data-key="craft_method_id" class="padding_0">
                                <select class="form-control input_no_border input-sm craft_method_id" name="craft_method_id" disabled>
                                    @foreach($_craft_methods as $vo)
                                        <option value="{{ $vo->id }}" {{ $vo->id == $item->craft_method_id ? 'selected' : '' }}>
                                            {{ $vo->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- 毛重 -->
                            <td data-key="gross_weight" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm gross_weight"
                                       name="gross_weight" value="{{ $item->gross_weight }}" readonly disabled>
                            </td>

                            <!-- 净重 -->
                            <td data-key="net_weight" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm net_weight"
                                       name="net_weight" value="{{ $item->net_weight }}" readonly disabled>
                            </td>

                            <!-- 备注 -->
                            <td data-key="remark" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm remark"
                                       name="remark" value="{{ $item->remark }}" readonly disabled>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>

                    <tfoot>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>唛头</th>
                        <th>起始</th>
                        <th>截止</th>
                        <th>仓库</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>库存</th>
                        <th>品牌</th>
                        <th>箱数<br><span id="total_carton_qty"></span></th>
                        <th>单箱数</th>
                        <th>总数<br><span id="total_quantity"></span></th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额<br><span id="total_amount"></span></th>
                        <th>长</th>
                        <th>宽</th>
                        <th>高</th>
                        <th>CBM<br><span id="total_cbm"></span></th>
                        <th>工序</th>
                        <th>毛重</th>
                        <th>净重</th>
                        <th>备注</th>
                    </tr>
                    <tr class="hidden" id="timg">
                        <th colspan="25"><img src="/images/timg.gif"/>正在提交中...</th>
                    </tr>
                    </tfoot>
                </table>

            </form>
        </div>
    </div>

    <div class="form-group container" style="margin-top: 20px;">
        <label>其他备注:</label>
        <textarea class="form-control" rows="5" name="comment" readonly disabled>{{ $outbound->comment }}</textarea>
    </div>

    <div id="success" class="text-center text-info" style="display:none; margin-top:20px;">
        <div><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;查看成功</div>
    </div>
@endsection

@section('script_js')
    <script>
        var CURRENT_CUSTOMER_DEFAULT_GOODS = @json($goods ?? collect());
        var outboundId = {{ $outbound->id }};

        $(function () {
            refreshSerial();
            totalAll();

            function refreshSerial() {
                $(".serial_number").each((i, el) => $(el).text(i + 1));
            }

            // 合计
            function totalAll() {
                sum("carton_qty", 0);
                sum("quantity", 0);
                sum("amount", 2);
            }

            function sum(field, dec) {
                let total = 0;
                $("." + field).each(function () {
                    total += Number($(this).val() || 0);
                });
                $("#total_" + field).text(total.toFixed(dec));
            }

            // 刷新下拉框
            $('.selectpicker').selectpicker('refresh');
        });
    </script>
@endsection
