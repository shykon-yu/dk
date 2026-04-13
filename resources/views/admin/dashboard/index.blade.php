@extends('admin.layouts.base')
@section('title','首页')
@section('content')
    <div class="xb6">
        <div class="xb12 body-tit">
            <h4>基本信息</h4>
        </div>

        <div class="xb6 xm6 xs12">
            <div class="info-bg">
                <div class="info-tit">
                    <a href="#">
                        <h4>近七日入库数量</h4>
                    </a>
                </div>
                <div class="icon-tree info-ico"></div>
                <span class="info-num">
                <span id="log_out"></span>
            </span>
            </div>

            <div class="info-bg pink">
                <div class="info-tit">
                    <a href="#">
                        <h4>昨日生产成材</h4>
                    </a>
                </div>
                <div class="icon-cubes info-ico"></div>
                <span class="info-num">
                <span></span>m<sup>3</sup>
            </span>
            </div>
        </div>

        <div class="xb6 xm6 xs12">
            <div class="info-bg yellow">
                <div class="info-tit">
                    <a href="#">
                        <h4>近七日帽子出库数量</h4>
                    </a>
                </div>
                <div class="info-ico icon-external-link"></div>
                <span class="info-num"></span>
            </div>

            <div class="info-bg green">
                <div class="info-tit">
                    <a href="#">
                        <h4>昨日综合出材率</h4>
                    </a>
                </div>
                <div class="info-ico icon-stack-overflow"></div>
                <span class="info-num"></span>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped s-table">
        <thead>
        <form method="get" action="{{ route('admin.dashboard') }}">
            <tr>
                <td colspan="18">
                    <div class="xb8 s-table-thead">产品出库报表</div>
                    <div class="input-group xb4 form-inline">
                        <div class="input-group">
                            <input id="start_date" type="text" name="start_date" autocomplete="off"
                                   placeholder="开始日期" value="{{ request('start_date') }}"
                                   class="form-control input-sm">
                            <span class="input-group-addon"><span class="icon-calendar"></span></span>
                        </div>
                        <div class="input-group">
                            <input id="end_date" type="text" name="end_date" autocomplete="off"
                                   placeholder="结束日期" value="{{ request('end_date') }}"
                                   class="form-control input-sm">
                            <span class="input-group-addon"><span class="icon-calendar"></span></span>
                        </div>
                        <button class="button button-small bg-blue">搜索</button>
                    </div>
                </td>
            </tr>
        </form>

        <tr>
            <th>序号</th>
            <th>客户</th>
            <th>类目</th>
            <th>数量</th>
            <th>金额</th>
        </tr>
        </thead>
        <tbody>
{{--        @foreach($outbound_list as $k => $vo)--}}
{{--            <tr>--}}
{{--                <td>{{ $k+1 }}</td>--}}
{{--                <td>{{ $vo['custom_name'] ?? '' }}</td>--}}
{{--                <td>{{ $vo['products_category_name'] ?? '' }}</td>--}}
{{--                <td>{{ $vo['sum_outbound_number'] ?? 0 }}</td>--}}
{{--                <td>{{ $vo['currency_icon'] ?? '' }}&nbsp;{{ $vo['sum_money'] ?? 0 }}</td>--}}
{{--            </tr>--}}
{{--        @endforeach--}}
        </tbody>
    </table>
@endsection
