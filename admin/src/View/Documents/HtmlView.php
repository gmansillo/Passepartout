<?php
namespace GiovanniMansillo\Component\Dory\Administrator\View\Documents;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of documents.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The search tools form
     *
     * @var    Form
     * @since  1.6
     */
    public $filterForm;

    /**
     * The model state
     *
     * @var    Registry
     * @since  1.6
     */
    public $state;

    /**
     * An array of items
     *
     * @var    array
     * @since  1.6
     */
    public $items = [];

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  1.6
     */
    public $pagination;

    /**
     * The active search filters
     *
     * @var    array
     * @since  1.6
     */
    public $activeFilters = [];

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

    /**
     * Method to display the view.
     *
     * @param   string  $tpl  A template file to load. [optional]
     *
     * @return  void
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function display($tpl = null): void
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items'); // Calls the getItems() method in the model, which retrieves the items from the database
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
            $this->setLayout('emptystate');
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar(): void
    {
        $canDo = ContentHelper::getActions('com_dory', 'category', $this->state->get('filter.category_id'));
        $user = $this->getCurrentUser();
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_DORY_MANAGER_DOCUMENTS'), 'file dory');

        if ($canDo->get('core.create') || \count($user->getAuthorisedCategories('com_dory', 'core.create')) > 0) {
            $toolbar->addNew('document.add');
        }


        if ((!$this->isEmptyState && ($canDo->get('core.edit.state') ) || ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))) {

            /** @var  DropdownButton $dropdown */
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state')) {
                if ($this->state->get('filter.published') != 2) {
                    $childBar->publish('documents.publish')->listCheck(true);

                    $childBar->unpublish('documents.unpublish')->listCheck(true);
                }

                if ($this->state->get('filter.published') != -1) {
                    if ($this->state->get('filter.published') != 2) {
                        $childBar->archive('documents.archive')->listCheck(true);
                    } elseif ($this->state->get('filter.published') == 2) {
                        $childBar->publish('publish')->task('documents.publish')->listCheck(true);
                    }
                }

                $childBar->checkin('documents.checkin');

                if ($this->state->get('filter.published') != -2) {
                    $childBar->trash('documents.trash')->listCheck(true);
                }
            }

            if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
                $toolbar->delete('documents.delete', 'JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }
        }

        if ($user->authorise('core.admin', 'com_dory') || $user->authorise('core.options', 'com_dory')) {
            $toolbar->preferences('com_dory');
        }

        $toolbar->help('Documents');
    }
}