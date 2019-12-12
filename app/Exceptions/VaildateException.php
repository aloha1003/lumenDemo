<?php
namespace App\Exceptions;

class VaildateException extends \Exception
{
    protected $message;
    protected $code;
    private $column;

    public function __construct(string $column, string $message, int $code = 100001)
    {
        $this->column = $column;
        $this->message = $message;
        $this->code = $code;
    }

    public function render()
    {
        $data = (app()->environment('production')) ? [] : [
            'column' => $this->column,
            'message' => $this->message,
        ];
        return response()->error($data, $this->code);
    }
}
