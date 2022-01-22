# Dcat Admin Extension 异常报告扩展

## 依赖
- php  | >= 7.4.0
- dcat/laravel-admin  | >= ~2.0

## 安装

#### composer 拉取代码
`composer require weiaibaicai/dcat-exception-reporter`

#### 执行迁移文件
`php artisan migrate --path=vendor/weiaibaicai/dcat-exception-reporter/database/migrations`

#### 启用插件
后台菜单 -> 开发工具 -> 扩展 -> weiaibaicai.dcat-exception-reporter -> 升级 -> 启用

#### 添加后台菜单
后台菜单 -> 系统 -> 菜单 -> 新增，路径填写 `weiaibaicai/exception-reporters`

#### 最后一步，触发
打开 `app/Exceptions/Handler.php`文件, 在 `report` 方法中调用 `ExceptionReporterService::report()`方法
```php
<?php

namespace App\Exceptions;

...
use Weiaibaicai\DcatExceptionReporter\Services\ExceptionReporterService;

class Handler extends ExceptionHandler
{
    ...
    
    public function report($e)
    {
        if (class_exists(ExceptionReporterService::class) &&  $this->shouldReport($e)) {
            ExceptionReporterService::report(request(), $e);
        }
        parent::report($e);
    }
    
    ...
}
```

#### 安装问题
1. 发布文件时可能存在权限问题，记得给足权限。可在项目根目录执行 `chmod -R 755 public/vendor`

#### 使用问题
1. 如果怕数据量太大，可以定时执行命令`php artisan exception-reporter:clear`来清空一周以前的异常
2. 迁移默认生成的数据表名为`exception_reporters`，也可以主动设置 `config('admin.extensions.dcat_exception_reporter.table')`的值来修改表明。这个值默认是没有的，主动添加一个即可

#### 特别感谢
1、感谢何总（old）的指导