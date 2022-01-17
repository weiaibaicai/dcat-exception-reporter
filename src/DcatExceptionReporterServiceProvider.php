<?php

namespace Weiaibaicai\DcatExceptionReporter;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;
use Encore\Admin\Reporter\Reporter;
use Weiaibaicai\DcatExceptionReporter\Console\ClearExceptionReporterCommand;

class DcatExceptionReporterServiceProvider extends ServiceProvider
{

    protected $commands = [
            ClearExceptionReporterCommand::class,
    ];

    protected $js  = [
        'js/index.js',
    ];
    protected $css = [
        'css/index.css',
    ];

    public function init()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'weiaibaicai.exception-reporter');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'weiaibaicai.exception-reporter');

        $this->app->booted(function () {
            Admin::app()->routes(function ($router) {
                $attributes = array_merge([
                    'prefix'     => config('admin.route.prefix'),
                    'middleware' => config('admin.route.middleware'),
                ], $this->config('route', []));
                $router->group($attributes, __DIR__ . '/Http/routes.php');
            });

//            Admin::requireAssets('@weiaibaicai.dcat-exception-reporter');

        });

        parent::init();
    }

    public function register()
    {
        $this->commands($this->commands);

    }

}
