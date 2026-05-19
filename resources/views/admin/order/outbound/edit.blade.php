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
            <span class="panel-tit">编辑出库</span>
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
                <select class="form-control input-sm" name="clearance_id" id="clearance_id">
                    @foreach($_clearances as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $outbound->clearance_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 支付方式 -->
                <select class="form-control input-sm" name="payment_id" id="payment_id">
                    @foreach($_payments as $vo)
                        <option value="{{ $vo->id }}" {{ $vo->id == $outbound->payment_id ? 'selected' : '' }}>
                            {{ $vo->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 胶带 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="tape" placeholder="胶带" value="{{ $outbound->tape }}">
                </div>

                <!-- 封箱号 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="seal_container_no" placeholder="封箱号" value="{{ $outbound->seal_container_no }}">
                </div>

                <!-- 出库日期 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="outbound_at" id="outbound_at" autocomplete="off"
                           placeholder="出库日期" type="text"
                           value="{{ $outbound->outbound_at ? \Carbon\Carbon::parse($outbound->outbound_at)->toDateString() : '' }}">
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
                                <div class="btn-row">
                                    <button type="button" class="btn btn-sm btn-danger goods_minus">-</button>
                                    <button type="button" class="btn btn-sm btn-success goods_plus">+</button>
                                    <button type="button" class="btn btn-sm btn-warning goods_mix">混</button>
                                </div>
                            </td>
                            <td class="serial_number">{{ $key+1 }}</td>

                            <!-- 子单ID 隐藏域 -->
                            <input type="hidden" name="id" class="item-id" value="{{ $item->id ?? '' }}">

                            <!-- 唛头 -->
                            <td data-key="shipping_mark" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm shipping_mark"
                                       name="shipping_mark" value="{{ $item->shipping_mark }}">
                            </td>

                            <!-- 起始 -->
                            <td data-key="carton_no_start" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_no_start"
                                       name="carton_no_start" value="{{ $item->carton_no_start }}">
                            </td>

                            <!-- 截止 -->
                            <td data-key="carton_no_end" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_no_end"
                                       name="carton_no_end" value="{{ $item->carton_no_end }}">
                            </td>

                            <!-- 仓库 -->
                            <td data-key="warehouse_id" class="padding_0">
                                <select class="form-control input_no_border input-sm warehouse_id" name="warehouse_id">
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
                                        title="请选择产品">
=
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
                                <select name="sku_id" class="form-control input_no_border input-sm sku_id"">
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
                                       name="brand_logo" value="{{ $item->brand_logo }}">
                            </td>

                            <!-- 箱数 -->
                            <td data-key="carton_qty" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_qty"
                                       name="carton_qty" value="{{ $item->carton_qty }}">
                            </td>

                            <!-- 单箱数量 -->
                            <td data-key="unit_carton_qty" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm unit_carton_qty"
                                       name="unit_carton_qty" value="{{ $item->unit_carton_qty }}">
                            </td>

                            <!-- 数量 -->
                            <td data-key="quantity" class="padding_0">
                                <input type="text" size="5" class="form-control input_no_border input-sm quantity"
                                       name="quantity" value="{{ $item->quantity }}" readonly>
                            </td>

                            <!-- 货币 -->
                            <td data-key="currency_id" class="padding_0">
                                <select class="form-control input_no_border input-sm currency_id" name="currency_id">
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
                                       name="price" value="{{ $item->price }}">
                            </td>

                            <!-- 金额 -->
                            <td data-key="amount" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm amount"
                                       name="amount" readonly value="{{ $item->amount }}">
                            </td>

                            <!-- 长 -->
                            <td data-key="carton_length" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_length"
                                       name="carton_length" value="{{ $item->carton_length }}">
                            </td>

                            <!-- 宽 -->
                            <td data-key="carton_width" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_width"
                                       name="carton_width" value="{{ $item->carton_width }}">
                            </td>

                            <!-- 高 -->
                            <td data-key="carton_height" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm carton_height"
                                       name="carton_height" value="{{ $item->carton_height }}">
                            </td>

                            <!-- CBM -->
                            <td data-key="cbm" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm cbm"
                                       name="cbm" readonly value="{{ $item->cbm }}">
                            </td>

                            <!-- 工序 -->
                            <td data-key="craft_method_id" class="padding_0">
                                <select class="form-control input_no_border input-sm craft_method_id" name="craft_method_id">
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
                                       name="gross_weight" value="{{ $item->gross_weight }}">
                            </td>

                            <!-- 净重 -->
                            <td data-key="net_weight" class="padding_0">
                                <input type="text" size="2" class="form-control input_no_border input-sm net_weight"
                                       name="net_weight" value="{{ $item->net_weight }}">
                            </td>

                            <!-- 备注 -->
                            <td data-key="remark" class="padding_0">
                                <input type="text" class="form-control input_no_border input-sm remark"
                                       name="remark" value="{{ $item->remark }}">
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

                <button class="btn btn-success pull-right" type="submit" id="p_confirm">保存</button>
            </form>
        </div>
    </div>

    <div class="form-group container" style="margin-top: 20px;">
        <label>其他备注:</label>
        <textarea class="form-control" rows="5" name="comment">{{ $outbound->comment }}</textarea>
    </div>

    <div id="success" class="text-center text-info" style="display:none; margin-top:20px;">
        <div><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;修改成功</div>
    </div>
@endsection

@section('script_js')
    <script>
        var CURRENT_CUSTOMER_DEFAULT_GOODS = @json($goods ?? collect());
        var GOODS_SEARCH_KEYWORD = '';
        var outboundId = {{ $outbound->id }};

        $(function () {
            $("#outbound_at").datepicker({maxDate: 0});
            refreshSerial();
            totalAll();

            // 回车
            $(document).on("keyup", "input", function (e) {
                if (e.keyCode === 13) {
                    let name = $(this).attr("name");
                    $(this).parents("tr").next().find(`input[name=${name}]`).select();
                }
            });

            // 加行
            $(document).on("click", ".goods_plus", function () {
                let _this_tr = $(this).parents("tr");
                let main_image = _this_tr.find('.thumb-img').data('src');
                let carton_no_start = parseInt(_this_tr.find('.carton_no_end').val()) || 0;
                carton_no_start++;

                let tr = _this_tr.clone();

                // 🔥 新行清空ID
                tr.find('.item-id').val('');

                _this_tr.after(tr);
                _this_tr.next().find('.thumb-img').data('src', main_image);
                _this_tr.next().find('.bootstrap-select').find("button:first").remove();
                _this_tr.next().find('.selectpicker').selectpicker("val");
                _this_tr.next().find("input,select").not(".brand_logo,.warehouse_id,.goods_id," +
                    ".shipping_mark,.carton_no_start,.carton_no_end,.carton_qty,.unit_carton_qty,.currency_id," +
                    ".carton_length,.carton_width,.carton_height,.craft_method_id").val("");
                _this_tr.next().find('.carton_no_start,.carton_no_end').val(carton_no_start);
                _this_tr.next().find('.selectpicker').selectpicker('refresh');
                _this_tr.next().find('.selectpicker').selectpicker('render');

                refreshSerial();
            });

            // 减行
            $(document).on("click", ".goods_minus", function () {
                if ($("#log tr").length <= 1) {
                    alert("至少保留一行");
                    return;
                }
                $(this).parents("tr").remove();
                refreshSerial();
                totalAll();
            });

            // 混行（克隆，但箱号起始截止不变）
            $(document).on("click", ".goods_mix", function () {
                let _this_tr = $(this).parents("tr");
                let main_image = _this_tr.find('.thumb-img').data('src');

                // 🔥 关键区别：不++，直接用原来的值
                let carton_no_start = parseInt(_this_tr.find('.carton_no_start').val()) || 0;

                let tr = _this_tr.clone();

                // 新行清空ID
                tr.find('.item-id').val('');

                _this_tr.after(tr);
                _this_tr.next().find('.thumb-img').data('src', main_image);
                _this_tr.next().find('.bootstrap-select').find("button:first").remove();
                _this_tr.next().find('.selectpicker').selectpicker("val");
                _this_tr.next().find("input,select").not(".brand_logo,.warehouse_id,.goods_id," +
                    ".shipping_mark,.carton_no_start,.carton_no_end,.carton_qty,.unit_carton_qty,.currency_id," +
                    ".carton_length,.carton_width,.carton_height,.craft_method_id").val("");

                // 🔥 克隆行的起始、截止 = 原行的值（不+1）
                _this_tr.next().find('.carton_no_start').val(carton_no_start);
                _this_tr.next().find('.carton_no_end').val(carton_no_start);

                _this_tr.next().find('.selectpicker').selectpicker('refresh');
                _this_tr.next().find('.selectpicker').selectpicker('render');

                refreshSerial();
            });

            function refreshSerial() {
                $(".serial_number").each((i, el) => $(el).text(i + 1));
            }

            // 计算
            $(document).on("input propertychange", ".p_body input", function () {
                calcRow($(this).parents("tr"));
                totalAll();
            });

            function calcRow(tr) {
                let carton_qty = Number(tr.find(".carton_qty").val() || 0);
                let unit_qty = Number(tr.find(".unit_carton_qty").val() || 0);
                let qty = carton_qty * unit_qty;
                tr.find(".quantity").val(qty);

                let price = Number(tr.find(".price").val() || 0);
                tr.find(".amount").val((qty * price).toFixed(2));

                // let l = Number(tr.find(".carton_length").val() || 0);
                // let w = Number(tr.find(".carton_width").val() || 0);
                // let h = Number(tr.find(".carton_height").val() || 0);
                // let cbm = (l * w * h * carton_qty);
                // tr.find(".cbm").val(cbm.toFixed(2));

                let gw = Number(tr.find(".gross_weight").val() || 0);
                tr.find(".net_weight").val((gw > 0 ? gw - 2 : 0).toFixed(2));
            }

            // 合计
            function totalAll() {
                sum("carton_qty", 0);
                sum("quantity", 0);
                sum("amount", 2);
                //sum("cbm", 2);
            }

            function sum(field, dec) {
                let total = 0;
                $("." + field).each(function () {
                    total += Number($(this).val() || 0);
                });
                $("#total_" + field).text(total.toFixed(dec));
            }

            $(document).on('input', '.carton_no_start', function () {
                let row = $(this).closest('tr');
                let start = parseInt($(this).val()) || 0;
                let endInput = row.find('.carton_no_end');
                let end = parseInt(endInput.val()) || 0;
                if (endInput.val() === '' || end < start) {
                    endInput.val(start);
                }
            });


            // 仓库 → 商品
            $(document).on('change', '.warehouse_id', function () {
                let customer_id = $('#customer_id').val();
                let warehouse_id = $(this).val();
                let _tr = $(this).closest('tr');
                let $select = _tr.find("[name='goods_id']");
                if (!customer_id) {
                    alert('请选择客户');
                    $(this).val("");
                    $select.html('').selectpicker('refresh');
                    CURRENT_CUSTOMER_DEFAULT_GOODS = [];
                    return;
                }
                GOODS_SEARCH_KEYWORD = '';
                $.ajax({
                    url: "{{ route('admin.common.customer-stock-goods') }}",
                    type: 'POST',
                    data: {customer_id,warehouse_id, _token: "{{ csrf_token() }}"},
                    success: res => {
                        if (res.code === 200) {
                            CURRENT_CUSTOMER_DEFAULT_GOODS = res.data;
                            let str = "";
                            $.each(res.data, (i, item) => {
                                str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                            });
                            $select.html(str).selectpicker('refresh').selectpicker('render');
                        }
                    }
                });
            });


            // 商品 → 颜色
            $(document).on('change', '.goods_id', function () {
                let goods_id = $(this).val();
                let _this = $(this).parents('tr');
                let warehouse_id = _this.find('.warehouse_id').val();
                _this.find('.sku_id').html("<option>请选择</option>");
                _this.find('.price').val('');
                if (!goods_id) return false;

                $.ajax({
                    url: "{{ route('admin.common.sku-by-goods') }}",
                    type: 'POST',
                    data: {goods_id,warehouse_id, _token: "{{ csrf_token() }}"},
                    success: res => {
                        if (res.code === 200) {
                            let thumb = "{{ asset(':url') }}".replace(':url', res.data.goods.thumb_image);
                            let main = "{{ asset(':url') }}".replace(':url', res.data.goods.main_image);
                            _this.find('img.thumb-img,img.hover-preview').attr('src', thumb).data('src', main);
                            let str = "<option>-请选择颜色-</option>";
                            $.each(res.data.skus, (i, item) => {
                                str += `<option value="${item.id}">${item.color}</option>`;
                            });
                            _this.find('.sku_id').html(str);
                            // _this.data('skuList', res.data.skus);
                            _this.find('.brand_logo').val(res.data.goods.brand_logo);
                        }
                    }
                });
            });

            // 颜色 → 单价
            $(document).on('change', '.sku_id', function () {
                let $sku = $(this);
                let sku_id = $sku.val();
                let $tr = $sku.parents('tr');
                let warehouse_id = $tr.find('.warehouse_id').val();
                if( !warehouse_id ){
                    alert('请选择仓库');
                    return false;
                }

                $.ajax({
                    url: "{{ route('admin.common.get-stock-info') }}",
                    type: 'POST',
                    data: {sku_id,warehouse_id, _token: "{{ csrf_token() }}"},
                    success: res => {
                        if (res.code === 200) {
                            $tr.find('.price').val(res.data.sku.sell_price);
                            $tr.find('.currency_id').val(res.data.sku.sell_currency_id);
                            $tr.find('.stock').html(res.data.stock);
                            calcRow($(this).parents("tr"));
                            totalAll();
                        }
                    }
                });
            });

            // ====================== 提交【编辑】 ======================
            $("#order").validate({
                rules: {
                    department_id: "required",
                    customer_id: "required",
                    clearance_id: "required",
                    payment_id: "required",
                    tape: "required",
                    outbound_at: "required",
                },
                messages: {
                    department_id: "请选择部门",
                    customer_id: "请选择客户",
                    clearance_id: "请选择清关方式",
                    payment_id: "请选择支付方式",
                    tape: "请输入胶带",
                    outbound_at: "请选择出库日期",
                },
                submitHandler: function (form) {
                    $("#p_confirm").prop('disabled', true).text('保存中...');
                    $("#timg").removeClass("hidden");
                    let formData = new FormData();

                    formData.append('_method', 'PUT');
                    formData.append('department_id', $("#department_id").val());
                    formData.append('customer_id', $("#customer_id").val());
                    formData.append('clearance_id', $("#clearance_id").val());
                    formData.append('payment_id', $("#payment_id").val());
                    formData.append('tape', $("input[name=tape]").val());
                    formData.append('seal_container_no', $("input[name=seal_container_no]").val());
                    formData.append('outbound_at', $("#outbound_at").val());
                    formData.append('outbound_code', $("#outbound_code").text());
                    formData.append('comment', $("textarea[name=comment]").val());

                    $(".p_body tr").each(function (index) {
                        let t = $(this);
                        formData.append(`goods[${index}][id]`, t.find(".item-id").val());
                        formData.append(`goods[${index}][brand_logo]`, t.find(".brand_logo").val());
                        formData.append(`goods[${index}][warehouse_id]`, t.find(".warehouse_id").val());
                        formData.append(`goods[${index}][goods_id]`, t.find("[name='goods_id']").val());
                        formData.append(`goods[${index}][sku_id]`, t.find(".sku_id").val());
                        formData.append(`goods[${index}][shipping_mark]`, t.find(".shipping_mark").val());
                        formData.append(`goods[${index}][carton_no_start]`, t.find(".carton_no_start").val());
                        formData.append(`goods[${index}][carton_no_end]`, t.find(".carton_no_end").val());
                        formData.append(`goods[${index}][carton_qty]`, t.find(".carton_qty").val());
                        formData.append(`goods[${index}][unit_carton_qty]`, t.find(".unit_carton_qty").val());
                        formData.append(`goods[${index}][quantity]`, t.find(".quantity").val());
                        formData.append(`goods[${index}][currency_id]`, t.find(".currency_id").val());
                        formData.append(`goods[${index}][price]`, t.find(".price").val());
                        formData.append(`goods[${index}][carton_length]`, t.find(".carton_length").val());
                        formData.append(`goods[${index}][carton_width]`, t.find(".carton_width").val());
                        formData.append(`goods[${index}][carton_height]`, t.find(".carton_height").val());
                        formData.append(`goods[${index}][craft_method_id]`, t.find(".craft_method_id").val());
                        formData.append(`goods[${index}][gross_weight]`, t.find(".gross_weight").val());
                        formData.append(`goods[${index}][net_weight]`, t.find(".net_weight").val());
                        formData.append(`goods[${index}][remark]`, t.find(".remark").val());
                    });

                    $.ajax({
                        url: "{{ route('admin.outbounds.update', $outbound->id) }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        dataType: "json",
                        success: res => {
                            if (res.code === 200) {
                                $("#success").show();
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                alert(res.msg);
                                $("#p_confirm").prop('disabled', false).text('保存');
                                $("#timg").addClass("hidden");
                            }
                        },
                        error: res => {
                            let msg = res.responseJSON?.msg || '系统异常';
                            alert(msg);
                            $("#p_confirm").prop('disabled', false).text('保存');
                            $("#timg").addClass("hidden");
                        }
                    });
                }
            });

        });
    </script>
@endsection
