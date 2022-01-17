<?php

namespace Weiaibaicai\DcatExceptionReporter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Dcat\Admin\Traits\HasDateTimeFormatter;


/**
 * Weiaibaicai\DcatExceptionReporter\Models\SystemException
 *
 * @property int         $id
 * @property string      $type
 * @property string      $code
 * @property string      $message
 * @property string      $file
 * @property int         $line
 * @property string      $trace
 * @property string      $method
 * @property string      $path
 * @property string      $query
 * @property string      $body
 * @property string      $cookies
 * @property string      $headers
 * @property string      $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ExceptionReporter extends Model
{
    use HasDateTimeFormatter;

    public const METHOD_COLOR = [
        'GET'     => 'green',
        'POST'    => 'yellow',
        'PUT'     => 'blue',
        'DELETE'  => 'red',
        'PATCH'   => 'black',
        'OPTIONS' => 'grey',
    ];

    /**
     * Settings constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection(config('admin.database.connection') ? : config('database.default'));

        $this->setTable(config('admin.extensions.dcat_exception_reporter.table', 'exception_reporters'));
    }

}