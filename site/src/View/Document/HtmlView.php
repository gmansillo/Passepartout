<?php

namespace GiovanniMansillo\Component\Passepartout\Site\View\Document;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\MVC\View\GenericDataException;

class HtmlView extends BaseHtmlView
{
    public $item;

    public function display($tpl = null): void
    {
        $this->item = $this->get('Item');
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode(
                "\n",
                $errors
            ), 500);
        }
        parent::display($tpl);
    }
}
