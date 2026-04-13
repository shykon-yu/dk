<script src="/js/jquery-1.9.1.min.js"></script>
<!-- CSS 统一放在头部 -->
<link rel="stylesheet" href="/css/jquery-ui-1.9.2.custom.min.css">
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/bootstrap-select.min.css">
<link rel="stylesheet" href="/css/pintuer.css">
<link rel="stylesheet" href="/css/font-awesome.min.css">
<link rel="stylesheet" href="/css/admin.css">

@yield('extends_js')
@yield('extends_css')
<style>
    .rota {
        transform: rotate(180deg);
        display: inline-block;
        animation: 2s infinite linear;
    }
    button {
        padding: 5px 10px !important;
    }
    .body-tit {
        padding: 0 15px;
    }
    .no_order_list {
        height: 232px;
        width: 100%;
        overflow: hidden;
        padding: 0 20px;
    }
    .no_order_list li {
        list-style: circle;
        height: 25px;
        line-height: 25px;
        display: flex;
        cursor: pointer;
    }
    .warning-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .warning-time {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .rota {
        transform: rotate(90deg);
        display: inline-block;
    }

    /* 强制三级菜单靠左显示，不偏移、不跑到右边 */
    .leftnav ul li ul {
        margin-left: 0 !important;
        padding-left: 20px !important;
        left: auto !important;
        right: auto !important;
        position: static !important;
        float: none !important;
        display: none; /* 默认隐藏 */
    }

    /* 三级菜单里的文字靠左 */
    .leftnav ul li ul li {
        text-align: left !important;
    }
</style>
