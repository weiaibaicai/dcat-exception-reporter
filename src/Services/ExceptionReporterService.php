<?php

namespace Weiaibaicai\DcatExceptionReporter\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Weiaibaicai\DcatExceptionReporter\Models\ExceptionReporter;
use Dcat\Admin\Admin;

class ExceptionReporterService
{

    protected $whitelist = [
        NotFoundHttpException::class
    ];

    /**
     * @var Request
     */
    protected $request;

    /**
     * Reporter constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Request   $request
     * @param Throwable $exception
     *
     * @return mixed
     */
    public static function report(Request $request, Throwable $exception)
    {
        //当扩展不存在或禁用时，不再记录异常
        if (!Admin::extension()->enabled('weiaibaicai.dcat-exception-reporter')) {
            return;
        }

        $reporter = new static($request);
        if (!in_array(get_class($exception), $reporter->whitelist) ) {
            return $reporter->reportException($exception);
        }
    }

    /**
     * @param Throwable $exception
     *
     * @return bool
     */
    public function reportException(Throwable $exception)
    {
        $data = [
            // Request info.
            'method'    => $this->request->getMethod(),
            'ip'        => $this->request->getClientIps(),
            'path'      => $this->request->path(),
            'query'     => Arr::except($this->request->all(), ['_pjax', '_token', '_method', '_previous_']),
            'body'      => $this->request->getContent(),
            'cookies'   => $this->request->cookies->all(),
            'headers'   => Arr::except($this->request->headers->all(), 'cookie'),

            // Exception info.
            'exception' => get_class($exception),
            'code'      => $exception->getCode(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'message'   => $exception->getMessage(),
            'trace'     => $exception->getTraceAsString(),
        ];

        $data = $this->stringify($data);

        try {
            $this->store($data);
        } catch (Exception $e) {
//            $result = $this->reportException($e);
        }

//        return $result;
    }

    /**
     * Convert all items to string.
     *
     * @param $data
     *
     * @return array
     */
    public function stringify($data)
    {
        return array_map(function ($item) {
            return is_array($item) ? json_encode($item, JSON_OBJECT_AS_ARRAY) : (string)$item;
        }, $data);
    }

    /**
     * Store exception info to db.
     *
     * @param array $data
     *
     * @return bool
     */
    public function store(array $data)
    {
        $exception = new ExceptionReporter();

        $exception->type    = $data['exception'];
        $exception->code    = $data['code'];
        $exception->message = $data['message'];
        $exception->file    = $data['file'];
        $exception->line    = $data['line'];
        $exception->trace   = $data['trace'];
        $exception->method  = $data['method'];
        $exception->path    = $data['path'];
        $exception->query   = $data['query'];
        $exception->body    = $data['body'];
        $exception->cookies = $data['cookies'];
        $exception->headers = $data['headers'];
        $exception->ip      = $data['ip'];

        try {
            $exception->save();
        } catch (Exception $e) {
            //dd($e);
        }

        return $exception->save();
    }
}
