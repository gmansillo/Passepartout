<?php

/**
 * @package     GiovanniMansillo.Dory
 * @subpackage  com_dory
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GiovanniMansillo\Component\Dory\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use JText;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Methods supporting a list of document records.
 *
 * @since  1.6
 */
class DocumentsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @since   1.6
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'a.id',
                'title',
                'a.title',
                'state',
                'a.state',
                'access',
                'a.access',
                'hits',
                'a.hits'
            ];
        }

        parent::__construct($config);
    }


    // gets all the information submitted in the user request and recreates the state of the application
    protected function populateState($ordering = 'title', $direction = 'ASC')
    {
        $app = Factory::getApplication();
        $value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
        $this->setState('list.limit', $value);
        $value = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $value);
        $search = $this->getUserStateFromRequest($this->context .
            '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  \Joomla\Database\DatabaseQuery
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.alias'),
                    $db->quoteName('a.description'),
                    $db->quoteName('a.state'),
                    $db->quoteName('a.hits'),
                    $db->quoteName('c.title', 'category_title'),
                    $db->quoteName('a.category'),
                    $db->quoteName('a.access')
                ]
            )
        )
            ->join('LEFT', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.category'))
            ->from($db->quoteName('#__dory_documents', 'a'));

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->escape(trim($search), true);
            $search = str_replace(' ', '%', $search);
            $search = $db->quote('%' . $search . '%');
            $query->where('(a.title LIKE ' . $search . ')');
        }

        $orderCol = $this->state->get(
            'list.ordering',
            'a.title'
        );
        $orderDirn = $this->state->get(
            'list.direction',
            'ASC'
        );
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    public function getItems()
    {
        $labels = [
            'COM_DORY_ACCESS_OPTION_PUBLIC',
            'COM_DORY_ACCESS_OPTION_USERS',
            'COM_DORY_ACCESS_OPTION_USERGROUPS'
        ];

        return array_map(function ($el) use ($labels) {
            $el->access = JText::_($labels[$el->access]);
            return $el;
        }, parent::getItems());
    }
}
