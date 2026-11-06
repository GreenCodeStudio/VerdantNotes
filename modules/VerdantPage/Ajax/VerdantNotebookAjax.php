<?php

namespace VerdantPage\Ajax;

use VerdantPage\VerdantNotebook;

class VerdantNotebookAjax extends \Core\AjaxController
{
    public function insert($data){
        return (new VerdantNotebook())->insert($data);
    }
}
