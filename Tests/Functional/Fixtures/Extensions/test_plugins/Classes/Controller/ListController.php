<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\TestPlugins\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ListController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse('list');
    }

    public function showAction(): ResponseInterface
    {
        return $this->htmlResponse('show');
    }
}
