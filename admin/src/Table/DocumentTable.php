<?php
namespace GiovanniMansillo\Component\Dory\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class DocumentTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__dory_documents', 'id', $db);
    }

    public function check()
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $date = Factory::getDate();

        // Set title
        $this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

        // Set alias
        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = $date->format('Y-m-d-H-i-s');
        }

        //TODO : check duplicate aliases

        // Check for a valid category.
        if (!$this->category = (int) $this->category) {
            $this->setError(Text::_('JLIB_DATABASE_ERROR_CATEGORY_REQUIRED'));

            return false;
        }

        // Set created date if not set.
        if (!(int) $this->created) {
            $this->created = $date->toSql();
        }

        // Set modified to created if not set
        if (!$this->modified) {
            $this->modified = $this->created;
        }

        return true;
    }
}