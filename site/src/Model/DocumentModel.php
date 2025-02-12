<?php

namespace GiovanniMansillo\Component\Passepartout\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class DocumentModel extends BaseDatabaseModel
{
    protected $_item = null;

    protected function populateState()
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $id = $app->input->getInt('id');
        $this->setState('document.id', $id);
        $this->setState('document.params', $params);
    }

    function getItem($pk = null)
    {
        // @TODO: check user permissions somewhere

        $id = (int) $pk ?: (int) $this->getState('document.id');
        if (!$id) {
            throw new \Exception('Missing document id', 404);
        }
        if ($this->_item !== null && $this->_item->id != $id) {
            return $this->_item;
        }
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__passepartout_documents', 'a'))
            ->where($db->quoteName('a.id') . ' = ' . (int) $id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if (!empty($item)) {
            $this->_item = $item;
        }
        
        return $this->_item;
    }
}
