@extends('admin.layouts.app')

@section('extends_css')
    <style>
        #dialog_radius td {
            vertical-align: middle;
            text-align: center;
        }
        .img-hover-box:hover .hover-preview {
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">出库资料</span>
        </div>

        <div class="panel-body navbar-form">
            <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped" style="margin-top:2em;">
                <tr>
                    <th colspan="22" style="font-size:20px">物流单</th>
                </tr>
                <tr>
                    <th>唛头</th>
                    <th>사진</th>
                    <th>ITEM</th>
                    <th>LOGO</th>
                    <th>PRICE</th>
                    <th>PACKING</th>
                    <th>CTN</th>
                    <th>QUAN'T</th>
                    <th>AMOUNT</th>
                    <th>L</th>
                    <th>W</th>
                    <th>H</th>
                    <th>CBM</th>
                    <th>비고</th>
                    <th>재질</th>
                    <th>工序</th>
                    <th>唛头</th>
                    <th>担当</th>
                    <th>毛重</th>
                    <th>净重</th>
                    <th>总毛重</th>
                    <th>总净重</th>
                </tr>
                @php
                    $total_carton_qty = 0;
                    $total_cbm = 0;
                    $total_gross_weight = 0;
                    $total_net_weight = 0;
                @endphp
                @foreach($items as $index => $item)
                    @php
                        $firstShippingMark = [];
                        if( array_key_exists($index,$indexRowspan['shipping_mark_text']) ){
                            $firstShippingMark = true;
                            $total_carton_qty += $item->carton_qty;
                            $total_cbm += $item->cbm;
                            $total_gross_weight += bcmul($item->carton_qty,$item->gross_weight,2);
                            $total_net_weight += bcmul($item->carton_qty,$item->net_weight,2);
                        }
                        $rowspanShippingMark = $firstShippingMark ? $indexRowspan['shipping_mark_text'][$index] : 0;

                        $firstGoodsId = array_key_exists($index, $indexRowspan['goods_id'] ?? []);
                        $rowspanGoodsId = $firstGoodsId ? $indexRowspan['goods_id'][$index] : 0;
                    @endphp

                    <tr class="text-center">
                        <!-- 唛头 -->
                        @if($firstShippingMark)
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->shipping_mark_text }}</td>
                        @endif

                        <!-- 图片 / ITEM / LOGO / PRICE / PACKING -->
                        @if($firstGoodsId)
                            <td rowspan="{{ $rowspanGoodsId }}">
                                @if($item->goods->thumb_image)
                                    <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; line-height:40px;">
                                        <img src="{{ asset($item->goods->thumb_image) }}"
                                             class="thumb-img click-preview"
                                             data-src="{{ asset($item->goods->main_image) }}"
                                             style="height:40px; max-height:40px; width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">
                                        <img src="{{ asset($item->goods->thumb_image) }}"
                                             class="hover-preview"
                                             style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td rowspan="{{ $rowspanGoodsId }}">{{ $item->goods->name }}</td>
                            <td rowspan="{{ $rowspanGoodsId }}">{{ $item->brand_logo }}</td>
                            <td rowspan="{{ $rowspanGoodsId }}">{{ $item->currency->symbol }}{{ $item->price }}</td>
                            <td rowspan="{{ $rowspanGoodsId }}">{{ $item->unit_carton_qty }}PCS</td>
                        @endif

                        <!-- CTN -->
                        @if($firstShippingMark)
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->carton_qty }}CTNS</td>
                        @endif

                        <!-- QUANTITY / AMOUNT -->
                        @if($firstGoodsId)
                            <td rowspan="{{ $rowspanGoodsId }}">{{ $item->quantity }}CS</td>
                            <td rowspan="{{ $rowspanGoodsId }}">{{ $item->currency->symbol }}{{ $item->amount }}</td>
                        @endif

                        <!-- L / W / H / CBM -->
                        @if($firstShippingMark)
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->carton_length }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->carton_width }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->carton_height }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->cbm }}</td>
                        @endif

                        <!-- 비고 (颜色数量) -->
                        <td style="width:150px; white-space: normal;">{{ $item->color_text }}</td>

                        <!-- 재질 -->
                        @if($firstGoodsId)
                            <td rowspan="{{ $rowspanGoodsId }}" style="width:150px; white-space: normal;">{{ $item->goods->component_kr_text }}</td>
                        @endif

                        <!-- 工序 / 唛头 / 担当 / 毛重 / 净重 / 总毛重 / 总净重 -->
                        @if($firstShippingMark)
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->craftMethod->name }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->shipping_mark }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->outbound->department->name }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->gross_weight }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ $item->net_weight }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ bcmul($item->gross_weight, $item->carton_qty, 2) }}</td>
                            <td rowspan="{{ $rowspanShippingMark }}">{{ bcmul($item->net_weight, $item->carton_qty, 2) }}</td>
                        @endif
                    </tr>
                @endforeach

                <!-- 合计 -->
                <tr>
                    <th>합계</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $total_carton_qty }}CTNS</th>
                    <th>{{ $items->sum('quantity') }}PCS</th>
                    <th>{{ $items->first()->currency->symbol .' '.$items->sum('amount') }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $total_cbm }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $total_gross_weight }}</th>
                    <th>{{ $total_net_weight }}</th>
                </tr>

                <tfoot>
                <tr>
                    <td colspan="22">
                        <form method="post" action="__URL__/excel_logistics_info">
                            <input type="hidden" name="logistics_order_number" value="" id="excel_order_number">
                            <input type="button" class="btn btn-success btn-sm fix-right-btn" value="生成表格" id="excel_order_info">
                        </form>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
