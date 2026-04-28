<?php

namespace App\Services\Admin;
use App\Services\Admin\Goods\CurrencyService;
use App\Services\Admin\Goods\GoodsComponentService;
use App\Services\Admin\Goods\GoodsSeasonService;
use App\Services\Admin\Goods\GoodsCategoryService;
use App\Services\Admin\Goods\GoodsService;
use Illuminate\Support\Facades\Auth;
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
            '_departments_auth' => $this->getDepartmentsAuth(),
            '_customers'   => $this->getCustomers(),
            '_clearances'   => $this->getClearances(),
            '_payments'     => $this->getPayments(),
            '_suppliers'   => $this->getSuppliers(),
            '_goods_categories' => $this->getGoodsCategories(),
            '_goods_seasons' => $this->getGoodsSeasons(),
            '_goods_components' => $this->getGoodsComponents(),
            '_warehouses'    => $this->getWarehouses(),
            '_currencies'    => $this->getCurrencies(),
        ];
    }

    // 获取部门
    public function getDepartments()
    {
        return app(DepartmentService::class)->getCacheAll();
    }

    public function getDepartmentsAuth()
    {
        if( Auth::user() === 1 ){
            return app(DepartmentService::class)->getCacheAll();
        }else{
            return Auth::user()->departments;
        }
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
    public function getSuppliers()
    {
        return app(SupplierService::class)->getCacheAll();
    }

    //商品分类
    public function getGoodsCategories()
    {
        return app(GoodsCategoryService::class)->getCacheAll();
    }

    //商品季节
    public function getGoodsSeasons()
    {
        return app(GoodsSeasonService::class)->getCacheAll();
    }

    //商品成分
    public function getGoodsComponents()
    {
        return app(GoodsComponentService::class)->getCacheAll();
    }

    //仓库列表
    public function getWarehouses()
    {
        return app(WarehouseService::class)->getCacheAll();
    }

    public function getCurrencies()
    {
        return app(CurrencyService::class)->getCacheAll();
    }

}
