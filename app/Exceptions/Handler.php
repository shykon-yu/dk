<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->ajax()) {

                // 处理 Request 表单验证错误
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'code' => 422,
                        'msg'  => $e->validator->errors()->first(),
                    ], 422);
                }

                // 手动抛的异常
                return response()->json([
                    'code' => method_exists($e, 'getCode') && $e->getCode() ? $e->getCode() : 500,
                    'msg'  => $e->getMessage(),
                ]);
            }
        });
    }
}
