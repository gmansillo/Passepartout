<?php

namespace GiovanniMansillo\Component\Passepartout\Administrator\View\Document;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View to download document's file.
 *
 * @since  1.5
 */
class RawView extends BaseHtmlView
{
    /**
     * The Form object
     *
     * @var    Form
     * @since  1.5
     */
    protected $form;

    /**
     * The active item
     *
     * @var    object
     * @since  1.5
     */
    protected $item;

    /**
     * The model state
     *
     * @var    object
     * @since  1.5
     */
    protected $state;

    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @throws  \Exception
     * @since   1.5
     *
     */

    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @throws  \Exception
     * @since   1.6
     *
     */
    public function display($tpl = null): void
    {
        $this->form = $this->get('Form');
        $this->state = $this->get('State');
        $this->item = $this->get('Item');

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $file_data_decoded = json_decode($this->item->file);

        // TODO: Localize error message
        if (!$file_data_decoded)
            throw new GenericDataException("Invalid file data json: {$file_data_decoded}", 500);

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $filePath = JPATH_ADMINISTRATOR . "/components/com_passepartout/uploads/{$this->item->id}-{$file_data_decoded->name}";

        if (!file_exists($filePath)) {
            // TODO: raise a real 404 error
            $app->enqueueMessage("Error 404", "error");
            return;
        }

        $this->getDocument()->setMimeEncoding("application/octet-stream");

        $app->setHeader(
            "Content-Transfer-Encoding",
            "Binary",
            true
        );

        // FIXME Content-disposition header being ignored by browsers
        $app->setHeader(
            "Content-disposition",
            "attachment; filename=\"{$file_data_decoded->name}\"",
            true
        );

        echo file_get_contents($filePath);

        exit;
    }
}