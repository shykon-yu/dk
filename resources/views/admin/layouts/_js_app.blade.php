<!-- 1. 核心基础库（必须最先加载） -->
<script src="/js/jquery-1.9.1.min.js"></script>

<!-- 2. jQuery UI 全套（按官方依赖顺序） -->
<script src="/js/jquery-ui-1.9.2.custom.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.ui.datepicker.js"></script>
<script src="/js/jquery.ui.datepicker-zh-CN.js"></script>

<!-- 3. Bootstrap 框架（依赖 jQuery） -->
<script src="/js/bootstrap.js"></script>
<script src="/js/bootstrap-select.min.js"></script>

<!-- 4. 通用插件 / 工具类 -->
<script src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery.fix.clone.js"></script>
<script src="/js/tableFix.js"></script>

<!-- 5. 页面自定义扩展 JS（最后加载） -->
@yield('extends_js')
