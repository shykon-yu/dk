<?php

namespace App\Services\Admin;
//use App\Services\Admin\SupplierService;
// 你以后的其他 Service 都可以加进来

class ViewDataService
{
    /**
     * 获取所有全局共用下拉数据
     * 给 ViewComposer 专用
     */
    public function getAllCommonData()
    {
        return [
            '_departments' => $this->getDepartments(),
            '_customers'   => $this->getCustomers(),
            '_clearances'   => $this->getClearances(),
            '_payments'     => $this->getPayments(),
           // '_suppliers'   => $this->getSuppliers(),
            // 继续加...
        ];
    }

    // 获取部门
    public function getDepartments()
    {
        return app(DepartmentService::class)->getCacheAll();
    }

    // 获取客户
    public function getCustomers()
    {
        return app(CustomerService::class)->getCacheAll();
    }

    //获取清关方式
    public function getClearances()
    {
        return app(ClearanceService::class)->getCacheAll();
    }

    //获取支付方式
    public function getPayments()
    {
        return app(PaymentService::class)->getCacheAll();
    }
    // 获取供应商
//    public function getSuppliers()
//    {
//        return app(SupplierService::class)->getCacheAll();
//    }

    // 你以后只需要在这里加方法
}
