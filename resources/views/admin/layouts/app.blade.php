<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="author" content="后台管理系统">
    <title>@yield('title','后台管理')</title>
    <!-- 样式文件 -->
    @include('admin.layouts._css_app')
    <!-- 脚本文件 -->
    @include('admin.layouts._js_app')
</head>
<style>
    /* 原有样式保留，新增这行 */
    .modal-img-view { transform-origin: center center !important; }
    /* 悬浮预览显示 */
    .img-hover-box:hover .hover-preview {
        opacity: 1 !important;
    }
    /* 模态框图片 */
    #previewMainImage {
        transform-origin: center center;
    }

        /* 修复 bootstrap-select 多选选中高亮样式 */
        .bootstrap-select .dropdown-menu > li.active > a {
            background-color: #337ab7 !important;
            color: #fff !important;
        }
        .bootstrap-select .dropdown-menu > li.active:hover > a {
            background-color: #286090 !important;
            color: #fff !important;
        }
        /* 让多选框已选项标签显示蓝色 */
        .bootstrap-select .filter-option-inner-inner {
            color: #337ab7 !important;
            font-weight: 500 !important;
        }
    </style>
<body>

@yield('content')
<!-- 图片预览模态框 -->
<div class="modal fade" id="imgPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="width:90%; max-width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5>商品图片预览</h5>
            </div>
            <div class="modal-body text-center" style="min-height:500px; background:#f5f5f5; position:relative;">
                <img id="previewMainImage" class="modal-img-view" src="" style="max-width:100%; max-height:600px; object-fit:contain; transition:all 0.2s; transform:rotate(0deg) scale(1); position:relative; cursor:move;">            </div>
            <div class="modal-footer text-center">
                <button class="btn btn-sm btn-info" id="rotateBtn">↺ 旋转 90°</button>
                <button class="btn btn-sm btn-primary" id="zoomInBtn">🔍+ 放大</button>
                <button class="btn btn-sm btn-primary" id="zoomOutBtn">🔍- 缩小</button>
                <button class="btn btn-sm btn-default" id="resetBtn">⟲ 重置</button>
                <button class="btn btn-sm btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
@yield('script')
<script>
    let rotate = 0; // 旋转角度
    let scale = 1;  // 缩放比例
    let isDrag = false; // 是否拖动
    let startX, startY, moveX, moveY; // 拖动坐标
    let imgLeft = 0, imgTop = 0; // 图片偏移量

    // 点击缩略图打开模态框
    $(document).on('click', '.click-preview', function () {
        // 重置所有状态
        rotate = 0; scale = 1; imgLeft = 0; imgTop = 0;
        let src = $(this).data('src');
        const $img = $('#previewMainImage');
        $img.attr('src', src)
            .css({
                transform: `rotate(0deg) scale(1)`,
                left: 0,
                top: 0
            });
        $('#imgPreviewModal').modal('show');
    });

    // 旋转按钮
    $('#rotateBtn').click(function () {
        rotate += 90;
        updateImgStyle();
    });

    // 放大按钮
    $('#zoomInBtn').click(function () {
        scale = Math.min(3, scale + 0.2); // 最大放大3倍
        updateImgStyle();
    });

    // 缩小按钮
    $('#zoomOutBtn').click(function () {
        scale = Math.max(0.4, scale - 0.2); // 最小缩小0.4倍
        // 缩小时重置偏移，防止图片跑偏
        if (scale <= 1) { imgLeft = 0; imgTop = 0; }
        updateImgStyle();
    });

    // 重置按钮
    $('#resetBtn').click(function () {
        rotate = 0; scale = 1; imgLeft = 0; imgTop = 0;
        updateImgStyle();
    });

    // 鼠标滚轮放大缩小（模态框内）
    $('#imgPreviewModal').on('wheel', '.modal-img-view', function (e) {
        e.preventDefault(); // 阻止滚轮默认滚动页面
        // 向上滚放大，向下滚缩小
        if (e.originalEvent.deltaY < 0) {
            scale = Math.min(3, scale + 0.1); // 滚轮步长0.1，更细腻
        } else {
            scale = Math.max(0.4, scale - 0.1);
            if (scale <= 1) { imgLeft = 0; imgTop = 0; }
        }
        updateImgStyle();
    });

    // 鼠标拖动图片（按下）
    $('#imgPreviewModal').on('mousedown', '.modal-img-view', function (e) {
        if (scale <= 1) return; // 未放大时禁止拖动
        isDrag = true;
        startX = e.clientX - parseInt($(this).css('left'));
        startY = e.clientY - parseInt($(this).css('top'));
        $(this).css('cursor', 'grabbing'); // 鼠标样式变"抓取"
    });

    // 鼠标拖动图片（移动）
    $(document).on('mousemove', function (e) {
        if (!isDrag) return;
        e.preventDefault();
        moveX = e.clientX - startX;
        moveY = e.clientY - startY;
        // 赋值偏移量
        imgLeft = moveX;
        imgTop = moveY;
        updateImgStyle();
    });

    // 鼠标拖动图片（松开）
    $(document).on('mouseup', function () {
        isDrag = false;
        $('.modal-img-view').css('cursor', 'move'); // 恢复鼠标样式
    });

    // 模态框关闭时重置状态
    $('#imgPreviewModal').on('hidden.bs.modal', function () {
        rotate = 0; scale = 1; imgLeft = 0; imgTop = 0;
    });

    // 统一更新图片样式（核心方法，所有操作最终调用）
    function updateImgStyle() {
        const $img = $('#previewMainImage');
        $img.css({
            transform: `rotate(${rotate}deg) scale(${scale})`,
            left: `${imgLeft}px`,
            top: `${imgTop}px`
        });
    }
</script>
