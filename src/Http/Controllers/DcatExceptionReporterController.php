<?php

namespace Weiaibaicai\DcatExceptionReporter\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Weiaibaicai\DcatExceptionReporter\Models\ExceptionReporter;
use Weiaibaicai\DcatExceptionReporter\Tracer\Parser;
use Weiaibaicai\DcatExceptionReporter\DcatExceptionReporterServiceProvider;

class DcatExceptionReporterController extends AdminController
{
    protected $title = 'ExceptionReporter';

    protected function grid()
    {
        return Grid::make(ExceptionReporter::class, function (Grid $grid) {

            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->type()->display(function ($type) {
                $path = explode('\\', $type);

                return array_pop($path);
            });

            $grid->code();
            $grid->message()->style('width:400px')->display(function ($message) {
                return empty($message) ? '' : "<code>$message</code>";
            });
            $grid->column('request')->display(function () {
                $color = ExceptionReporter::METHOD_COLOR[$this->method];

                return sprintf('<span class="label bg-%s">%s</span><code>%s</code>', $color, $this->method,
                    $this->path);
            });
            $grid->input()->display(function ($input) {
                $input = json_decode($input, true);

                return empty($input) ? '' : '<pre>' . json_encode($input, JSON_PRETTY_PRINT) . '</pre>';
            });
            $grid->created_at();

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('type');
                $filter->like('message');
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
            });

            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new ExceptionReporter(), function (Show $show) use ($id) {
            $exception = ExceptionReporter::findOrFail($id);
            $trace     = "#0 {$exception->file}({$exception->line})\n";
            $frames    = (new Parser($trace . $exception->trace))->parse();
            $cookies   = json_decode($exception->cookies, true);
            $headers   = json_decode($exception->headers, true);

            array_pop($frames);

            Admin::requireAssets('@weiaibaicai.dcat-exception-reporter');
            $view = view('weiaibaicai.exception-reporter::index', compact('exception', 'frames', 'cookies', 'headers'));

            $show->html($view);
            $show->disableEditButton();
            $show->disableDeleteButton();
        });
    }



}