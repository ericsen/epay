<?php

// namespace App;

// use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laratrust\Traits\LaratrustUserTrait;

// class User extends Authenticatable
// {
//                     use LaratrustUserTrait;
//                     use Notifiable;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var array
//      */
//     protected $fillable = [
//                      'name', 'email', 'password',
//     ];

//     /**
//      * The attributes that should be hidden for arrays.
//      *
//      * @var array
//      */
//     protected $hidden = [
//                   'password', 'remember_token',
//     ];

//     /**
//      * The attributes that should be cast to native types.
//      *
//      * @var array
//      */
//                     protected $casts = [
//         'email_verified_at' => 'datetime',
//     ];


//     /**
//      * 提供給 laravel-debugbar show_name 使用
//      * debugbar Auth status 預設抓 username 欄位，當username不存在，則抓取 email 欄位
//      *
//      * @return string
//      */
//     public function getUsernameAttribute()
//     {
//         return $this->name;
//     }
// }
