<?php

// 严格模式
declare(strict_types=1);

namespace Kingbes;

/**
 * QuickJs class
 */
class QuickJs
{
    /**
     * FFI variable
     *
     * @var \FFI
     */
    protected \FFI $ffi;

    /**
     * 构造 function
     *
     * @param string $dir library文件夹
     */
    public function __construct(string $dir = "")
    {
        if ($dir === "") {
            $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "build" . DIRECTORY_SEPARATOR . "os";
        }
        switch (\PHP_OS_FAMILY) {
            case "Linux":
                $suffix = "so";
                break;
            case "Windows":
                $suffix = "dll";
                break;
            default:
                throw new \Exception("QuickJs仅支持windows系统和linux系统。" . PHP_EOL
                    . "QuickJs supports only windows and linux.");
        }

        $header_content =  file_get_contents($dir . DIRECTORY_SEPARATOR . "QuickJs.h");
        $library_file = $dir . DIRECTORY_SEPARATOR . "QuickJs." . $suffix;
        $this->ffi = \FFI::cdef($header_content, $library_file);
    }

    /**
     * 创建JS运行时 function
     *
     * @return \FFI\CData JS运行时对象
     */
    public function create(): \FFI\CData
    {
        return $this->ffi->quickjs_create();
    }

    /**
     * 执行代码 function
     *
     * @param \FFI\CData $run_time JS运行时对象
     * @param string $code JS代码
     * @return \FFI\CData 结果对象
     */
    public function eval(\FFI\CData $run_time, string $code): \FFI\CData
    {
        return $this->ffi->quickjs_eval($run_time, $code);
    }

    /**
     * 是否是异常 function
     *
     * @param \FFI\CData $run_time JS运行时对象
     * @param \FFI\CData $js_eval JS对象
     * @return boolean
     */
    public function isException(\FFI\CData $run_time, \FFI\CData $js_eval): bool
    {
        return !$this->ffi->quickjs_is_exception($run_time, $js_eval);
    }

    /**
     * 字符串 function
     *
     * @param \FFI\CData $run_time
     * @param \FFI\CData $js_obj
     * @return mixed
     */
    public function toString(\FFI\CData $run_time, \FFI\CData $js_obj): mixed
    {
        return $this->ffi->quick_js_ToCString($run_time, $js_obj);
    }

    /**
     * 释放JS运行时 function
     *
     * @param \FFI\CData $run_time JS运行时对象
     * @return void
     */
    public function free(\FFI\CData $run_time): void
    {
        $this->ffi->quickjs_free($run_time);
    }
}