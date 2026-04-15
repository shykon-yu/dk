<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
class UserRequest extends BaseRequest
{
    public function rules()
    {
        $sensitiveWords = ['admin', 'root', 'test']; // 敏感词列表
        return [
            'id' => ['nullable', 'integer', 'exists:users,id'],
            'username' => ['required', 'string', 'unique:users,username,'.$this->input('id') ,
                'between:3,100' , 'regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_]+$/u',
                function ($attribute, $value, $fail) use ($sensitiveWords) {
                    if($this->id == 1){return;}
                    foreach ($sensitiveWords as $word) {
                        if (str_contains(strtolower($value), $word)) {
                            $fail("{$attribute}用户名不能包含敏感词：{$word}");
                        }
                    }
                }],
            'name' => ['required', 'string',],
            'password' => $this->input('id')
                ? ['nullable', 'string', 'min:6']
                : ['required', 'string', 'min:6'],
            'email' => ['required', 'email','unique:users,email,'.$this->id],
            'phone_number' => ['nullable', 'string',],
            'open_id' => ['nullable', 'string',],
            'role_id' => ['required', 'exists:roles,id',],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => '用户名不能为空',
            'username.unique' => '用户名已存在',
            'name.required' => '姓名不能为空',
            'password.required' => '密码不能为空',
            'password.min' => '密码不能少于6位',
            'role_id.required' => '请选择角色',
            'role_id.exists' => '所选角色不存在',
        ];
    }
}
