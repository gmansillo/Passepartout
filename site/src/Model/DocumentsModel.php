<?php

namespace GiovanniMansillo\Component\Passepartout\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

class DocumentsModel extends ListModel
{
    protected function populateState($ordering = 'title', $direction = 'ASC')
    {
        $app = Factory::getApplication();
        $value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
        $this->setState('list.limit', $value);
        $value = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $value);
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.title'),
                    $db->quoteName('a.file_size'),
                ]
            )
        )->from($db->quoteName('#__passepartout_documents', 'a'));
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
}
