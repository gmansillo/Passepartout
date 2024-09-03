<?php
namespace GiovanniMansillo\Component\Dory\Administrator\View\Document;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $state;
    public $item;

    public function display($tpl = null): void
    {
        $this->form = $this->get('Form');
        $this->state = $this->get('State');
        $this->item = $this->get('Item');

        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the action buttons, Save, Apply, and Cancel, with the code Joomla! already provided for it
     * @return void
     */
    protected function addToolbar()
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $canDo = ContentHelper::getActions('com_dory');
        $toolbar = Toolbar::getInstance();
        ToolbarHelper::title(Text::_('COM_DORY_DOCUMENT_TITLE_' . ($isNew ? 'ADD' : 'EDIT')), "file dory");
        if ($canDo->get('core.create')) {
            $toolbar->apply('document.apply');
            $toolbar->save('document.save');
        }
        $toolbar->cancel('document.cancel', 'JTOOLBAR_CLOSE');
    }
}