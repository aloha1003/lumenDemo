<?php

namespace App\Traits;

trait AdminDialog
{
    public function dialog()
    {
        $this->confirm(__('actions.confirm_action', ['action' => $this->name()]));
    }
}
