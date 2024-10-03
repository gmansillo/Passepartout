<?php

/**
 * @package     GiovanniMansillo.Dory
 * @subpackage  com_dory
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GiovanniMansillo\Component\Dory\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\FormController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Documents list controller class.
 *
 * @since  1.6
 */
class DocumentsController extends AdminController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.6
     */
    protected $text_prefix = 'COM_DORY_DOCUMENTS';

    /**
     * Constructor.
     *
     * @param   array                 $config   An optional associative array of configuration settings.
     * @param   ?MVCFactoryInterface  $factory  The factory.
     * @param   ?CMSApplication       $app      The Application for the dispatcher
     * @param   ?Input                $input    Input
     *
     * @since   3.0
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        $this->registerTask('sticky_unpublish', 'sticky_publish');
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel  The model.
     *
     * @since   1.6
     */
    public function getModel($name = 'Document', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Stick items
     *
     * @return  void
     *
     * @since   1.6
     */
    public function sticky_publish()
    {
        // Check for request forgeries.
        $this->checkToken();

        $ids    = (array) $this->input->get('cid', [], 'int');
        $values = ['sticky_publish' => 1, 'sticky_unpublish' => 0];
        $task   = $this->getTask();
        $value  = ArrayHelper::getValue($values, $task, 0, 'int');

        // Remove zero values resulting from input filter
        $ids = array_filter($ids);

        if (empty($ids)) {
            $this->app->enqueueMessage(Text::_('COM_DORY_NO_DOCUMENTS_SELECTED'), 'warning');
        } else {
            // Get the model.
            /** @var \GiovanniMansillo\Component\Dory\Administrator\Model\DocumentModel $model */
            $model = $this->getModel();

            // Change the state of the records.
            if (!$model->stick($ids, $value)) {
                $this->app->enqueueMessage($model->getError(), 'warning');
            } else {
                if ($value == 1) {
                    $ntext = 'COM_DORY_N_DOCUMENTS_STUCK';
                } else {
                    $ntext = 'COM_DORY_N_DOCUMENTS_UNSTUCK';
                }

                $this->setMessage(Text::plural($ntext, \count($ids)));
            }
        }

        $this->setRedirect('index.php?option=com_dory&view=documents');
    }

    /**
     * Method to get the number of published documents for quickicons
     *
     * @return  void
     *
     * @since   4.3.0
     */
    public function getQuickiconContent()
    {
        $model = $this->getModel('documents');

        $model->setState('filter.published', 1);

        $amount = (int) $model->getTotal();

        $result = [];

        $result['amount'] = $amount;
        $result['sronly'] = Text::plural('COM_DORY_N_QUICKICON_SRONLY', $amount);
        $result['name']   = Text::plural('COM_DORY_N_QUICKICON', $amount);

        echo new JsonResponse($result);
    }
}
