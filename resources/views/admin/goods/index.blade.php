@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">商品管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.goods.index') }}" id="search">
                <input type="text" name="name" placeholder="商品名称" class="form-control input-sm" value="{{ request('name') }}">
                <input type="text" name="customer_sku" placeholder="客户SKU" class="form-control input-sm" value="{{ request('customer_sku') }}">

                <!-- 部门 -->
                <select name="department_ids[]" id="department_ids" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择部门">
                    @foreach($_departments as $dept)
                        <option value="{{ $dept->id }}"
                                @if(in_array($dept->id, (array)request('department_ids', [])))
                                    selected
                            @endif>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <select name="customer_ids[]" id="customer_ids" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择客户">
                    @foreach($_customers as $val)
                        <option value="{{ $val->id }}"
                                @if(in_array($val->id, (array)request('customer_ids', [])))
                                    selected
                            @endif>
                            {{ $val->name }}
                        </option>
                    @endforeach
                </select>

                <select name="supplier_ids[]" id="supplier_ids" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择供应商">
                    @foreach($_suppliers as $val)
                        <option value="{{ $val->id }}"
                                @if(in_array($val->id, (array)request('supplier_ids', [])))
                                    selected
                            @endif>
                            {{ $val->name }}
                        </option>
                    @endforeach
                </select>

                <select name="category_ids[]" id="category_ids" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择分类">
                    @foreach($_goods_categories as $val)
                        <option value="{{ $val->id }}"
                                @if(in_array($val->id, (array)request('category_ids', [])))
                                    selected
                            @endif>
                            {{ $val->name }}
                        </option>
                    @endforeach
                </select>

                <select name="season_ids[]" id="season_ids" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择季节">
                    @foreach($_goods_seasons as $val)
                        <option value="{{ $val->id }}"
                                @if(in_array($val->id, (array)request('season_ids', [])))
                                    selected
                            @endif>
                            {{ $val->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 状态 -->
                <select name="status[]" id="status" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择状态">
                    <option value="1" @if(in_array(1, (array)request('status', []))) selected @endif>启用</option>
                    <option value="0" @if(in_array(0, (array)request('status', []))) selected @endif>禁用</option>
                </select>

                <!-- 星标 -->
                <select name="is_star[]" id="is_star" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择星标">
                    <option value="1" @if(in_array(1, (array)request('is_star', []))) selected @endif>星标</option>
                    <option value="0" @if(in_array(0, (array)request('is_star', []))) selected @endif>非星标</option>
                </select>

                <button type="submit" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-warning btn-sm" id="R">重置</button>

                <a href="{{ route('admin.goods.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增商品
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="goods_table">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>图片</th>
                    <th>商品名称</th>
                    <th>客户SKU</th>
                    <th>部门</th>
                    <th>客户</th>
                    <th>供应商</th>
                    <th>品牌LOGO</th>
                    <th>分类</th>
                    <th>季节</th>
                    <th>状态</th>
                    <th>星标</th>
                    <th>排序</th>
                    <th>备注</th>
                    <th>创建人</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="goods_list">
                @foreach($list as $key => $vo)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            @if($vo->thumb_image)
                                <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; line-height:40px;">
                                    {{-- 缩略图 --}}
                                    <img src="{{ asset($vo->thumb_image) }}"
                                         class="thumb-img click-preview"
                                         data-src="{{ asset($vo->main_image) }}"
                                         style="height:40px; max-height:40px; width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">

                                    {{-- 右侧悬浮预览图 --}}
                                    <img src="{{ asset($vo->thumb_image) }}"
                                         class="hover-preview"
                                         style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $vo->name }}</td>
                        <td>{{ $vo->customer_sku }}</td>
                        <td>{{ $vo->department->name ?? '-' }}</td>
                        <td>{{ $vo->customer->name ?? '-' }}</td>
                        <td>{{ $vo->supplier->name ?? '-' }}</td>
                        <td>{{ $vo->brand_logo ?? '-' }}</td>
                        <td>{{ $vo->category->name ?? '-' }}</td>
                        <td>{{ $vo->season->name ?? '-' }}</td>
                        <td class="change-status" style="cursor:pointer" data-id="{{ $vo->id }}" data-status="{{ $vo->status }}">
                            @if($vo->status)
                                <span class="label label-success">启用</span>
                            @else
                                <span class="label label-default">禁用</span>
                            @endif
                        </td>
                        <td class="change-star" style="cursor:pointer" data-id="{{ $vo->id }}">
                            @if($vo->is_star)
                                <span class="glyphicon glyphicon-star text-warning" style="font-size:16px;"></span>
                            @else
                                <span class="glyphicon glyphicon-star-empty text-muted" style="font-size:16px;"></span>
                            @endif
                        </td>
                        <td>{{ $vo->sort }}</td>
                        <td>
                            <div data-toggle="tooltip"
                                 data-placement="top"
                                 data-container="body"
                                 title="{{ $vo->remark ?? '-' }}"
                                 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; display: inline-block;">
                                {{ Str::limit($vo->remark ?? '-', 25) }}
                            </div>
                        </td>
                        <td>{{ $vo->creator->name ?? '系统' }}</td>
                        <td>{{ $vo->created_at }}</td>
                        <td>
                            <a href="{{ route('admin.goods.edit', $vo) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_goods" data-id="{{ $vo->id }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="18" class="text-center pagelist">
                        {{ $list->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="18">
                        <button type="button" class="btn btn-danger btn-sm" onclick="Alldel()">批量删除</button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // 全选
        function checkAll() {
            let all = document.getElementById('all');
            let items = document.getElementsByName('one[]');
            for (let i = 0; i < items.length; i++) {
                items[i].checked = all.checked;
            }
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('.selectpicker').selectpicker();

            // 重置
            $('#R').click(function () {
                $('#search')[0].reset();
                $('.selectpicker').selectpicker('refresh');
                setTimeout(() => $('#T').click(), 100);
            });

            // 删除
            $("#goods_list").on('click', ".del_goods", function () {
                if (!confirm("确定删除？")) return false;
                let id = $(this).data('id');
                let tr = $(this).closest('tr');
                $.post("{{ route('admin.goods.destroy','') }}/" + id, {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                }, res => {
                    alert(res.msg);
                    if (res.code === 200) tr.remove();
                }).fail(err => alert(err.responseJSON?.msg || '失败'));
            });

            // 状态切换
            $("#goods_list").on('click', ".change-status", function () {
                let id = $(this).data('id');
                let now = $(this).data('status');
                let to = now == 1 ? 0 : 1;
                if (!confirm("确定" + (to ? "启用" : "禁用") + "？")) return false;

                $.post("{{ route('admin.goods.status','') }}/" + id, {
                    _token: "{{ csrf_token() }}",
                    status: to
                }, res => {
                    if (res.code === 200) {
                        let html = res.status == 1
                            ? '<span class="label label-success">启用</span>'
                            : '<span class="label label-default">禁用</span>';
                        $(this).html(html).data('status', res.status);
                    }
                    alert(res.msg);
                }).fail(err => alert('操作失败'));
            });

            // 星标切换
            $("#goods_list").on('click', ".change-star", function () {
                let id = $(this).data('id');
                let obj = $(this);
                $.post("{{ route('admin.goods.star','') }}/"+id, {
                    _token: "{{ csrf_token() }}",
                    id: id
                }, res => {
                    if (res.code === 200) {
                        let star = res.is_star
                            ? '<span class="glyphicon glyphicon-star text-warning" style="font-size:16px;"></span>'
                            : '<span class="glyphicon glyphicon-star-empty text-muted" style="font-size:16px;"></span>';
                        obj.html(star);
                    }
                    alert(res.msg);
                }).fail(err => alert('操作失败'));
            });
        });

        // 批量删除
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function () {
                ids.push($(this).val());
            });
            if (ids.length === 0) return alert("请选择");
            if (!confirm("确定删除选中？")) return false;

            $.post("{{ route('admin.goods.batch.destroy') }}", {
                _token: "{{ csrf_token() }}",
                ids: ids,
                _method: "DELETE"
            }, res => {
                alert(res.msg);
                if (res.code === 200) location.reload();
            }).fail(err => alert("失败"));
        }
    </script>
@endsection
