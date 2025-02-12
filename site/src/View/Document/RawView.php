<?php

namespace GiovanniMansillo\Component\Passepartout\Site\View\Document;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of tracks.
 *
 * @since  1.6
 */
class RawView extends BaseHtmlView
{
    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   1.6
     *
     * @throws  \Exception
     */
    public function display($tpl = null): void
    {
        /** @var DocumentModel $model */
        $model = $this->getModel();
        $item = $model->getItem();

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->getDocument()->setMimeEncoding('application/octet-stream');

        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $app->setHeader(
            'Content-disposition',
            'attachment; filename="' . basename($item->file_name) . '"; creation-date="' . Factory::getDate()->toRFC822() . '"',
            true
        );

        echo file_get_contents($item->file_name);
    }
}
