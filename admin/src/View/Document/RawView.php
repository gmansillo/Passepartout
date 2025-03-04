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
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   1.5
     *
     * @throws  \Exception
     */

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
        $this->form = $this->get('Form');
        $this->state = $this->get('State');
        $this->item = $this->get('Item');

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $file_data_decoded = json_decode($this->item->file);

        if (!$file_data_decoded)
            throw new GenericDataException("Invalid file data json: {$file_data_decoded}", 500); // TODO: Localize error message

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $filePath = JPATH_COMPONENT_ADMINISTRATOR . "/uploads/" . $this->item->id . "-" . $file_data_decoded->name;

        if (!file_exists($filePath)) {

            $app->enqueueMessage('ss', 'error');
            return;
            // TODO: raise a 404 error
            http_response_code(404);

            return;
        }

        $this->getDocument()->setMimeEncoding('application/octet-stream');


        $app->setHeader(
            'Content-disposition',
            'attachment; filename="' . $file_data_decoded->name . '"; creation-date="' . Factory::getDate()->toRFC822() . '"',
            true
        );
        echo file_get_contents($filePath);

        exit;
    }
}