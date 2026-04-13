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

        $(".leftnav h2").click(function () {
            $(this).next().slideToggle(200);
            $(this).toggleClass("on");
            $(this).find(".icon-caret-down").toggleClass("rota");
        });

        // 二级菜单点击 → 展开/关闭三级菜单
        $(".leftnav ul li a").click(function (e) {
            // 如果当前下面有三级菜单，就展开/折叠
            if ($(this).next("ul").length) {
                e.preventDefault(); // 阻止跳转，只展开菜单
                $(this).next("ul").slideToggle(200);
                $(this).find(".icon-caret-right").toggleClass("rota");
            }

            // 右侧内容切换（你原来的逻辑不动）
            $(".default-body").hide();
            $("iframe").show();
        });
    });
</script>
