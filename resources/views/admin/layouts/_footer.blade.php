<!-- JS 统一放在底部（优化加载速度） -->
<script src="/js/jquery.fix.clone.js"></script>
<script src="/js/jquery.validate.js"></script>
<script src="/js/jquery-ui-1.9.2.custom.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.ui.datepicker.js"></script>
<script src="/js/jquery.ui.datepicker-zh-CN.js"></script>
<script src="/js/bootstrap.js"></script>
<script src="/js/jquery.form.js"></script>
<script src="/js/jquery.mousewheel.min.js"></script>
<script src="/js/bootstrap-select.min.js"></script>

<script>
    document.getElementById('logout-btn').addEventListener('click', function() {
        document.getElementById('logout-form').submit();
    });
    $(function () {
        // 日期控件
        $("#start_date, #end_date").datepicker({ maxDate: 0 });

        // 左侧菜单折叠
        $(".leftnav h2").click(function () {
            $(this).next().slideToggle(200);
            $(this).toggleClass("on");
            $(this).find(".icon-caret-down").toggleClass("rota");
        });

        // 菜单点击切换
        $(".leftnav ul li a").click(function () {
            $(".default-body").hide();
            $("iframe").show();
        });
    });
</script>
