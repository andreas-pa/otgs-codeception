<?php

namespace Codeception\Module;

class GeneralHelpers extends \Codeception\Module
{
    public function getBaseUrlName(){

        return $this->getModule('WebDriver')->_getUrl();

    }
}