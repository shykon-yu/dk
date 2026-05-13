<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\NotificationEnum;
use Illuminate\Support\Facades\Redirect;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->get();
        return view('admin.notification.index', compact('notifications'));
    }

    public function show($id,$type)
    {
        if (!NotificationEnum::isValid($type)) {
            abort(403, '非法访问');
        }

        $route = NotificationEnum::getRoute($type);
        return Redirect::route($route, $id);
    }
}
