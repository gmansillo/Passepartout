<?php

/**
 * @package     GiovanniMansillo.Passepartout
 * @subpackage  com_passepartout
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GiovanniMansillo\Component\Passepartout\Administrator\Model;

use GiovanniMansillo\Component\Passepartout\Site\Helper\PassepartoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Table\TableInterface;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\Path;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Document model.
 *
 * @since  1.6
 */
class DocumentModel extends AdminModel
{
    use VersionableModelTrait;

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.6
     */
    protected $text_prefix = 'COM_PASSEPARTOUT_DOCUMENT';

    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  3.2
     */
    public $typeAlias = 'com_passepartout.document';

    /**
     * Batch copy/move command. If set to false, the batch copy/move command is not supported
     *
     * @var  string
     */
    protected $batch_copymove = 'category_id';

    /**
     * Allowed batch commands
     *
     * @var  array
     */
    protected $batch_commands = [
        'client_id' => 'batchClient',
        'language_id' => 'batchLanguage',
    ];

    protected $uploadFolder = JPATH_ADMINISTRATOR . '/components/com_passepartout/uploads/';


    /**
     * Data cleanup after batch copying data
     *
     * @param   TableInterface  $table  The table object containing the newly created item
     * @param   integer         $newId  The id of the new item
     * @param   integer         $oldId  The original item id
     *
     * @return  void
     *
     * @since  4.3.2
     */
    protected function cleanupPostBatchCopy(TableInterface $table, $newId, $oldId)
    {
        // Initialise clicks and impmade
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__passepartout_documents'))
            ->set($db->quoteName('clicks') . ' = 0')
            ->set($db->quoteName('impmade') . ' = 0')
            ->where($db->quoteName('id') . ' = :newId')
            ->bind(':newId', $newId, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }


    /**
     * Batch client changes for a group of documents.
     *
     * @param   string  $value     The new value matching a client.
     * @param   array   $pks       An array of row IDs.
     * @param   array   $contexts  An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     *
     * @since   2.5
     */
    protected function batchClient($value, $pks, $contexts)
    {
        // Set the variables
        $user = $this->getCurrentUser();

        /** @var \GiovanniMansillo\Component\Passepartout\Administrator\Table\DocumentTable $table */
        $table = $this->getTable();

        foreach ($pks as $pk) {
            if (!$user->authorise('core.edit', $contexts[$pk])) {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

                return false;
            }

            $table->reset();
            $table->load($pk);
            $table->cid = (int) $value;

            if (!$table->store()) {
                $this->setError($table->getError());

                return false;
            }
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canDelete($record)
    {
        if (empty($record->id) || $record->state != -2) {
            return false;
        }

        if (!empty($record->catid)) {
            return $this->getCurrentUser()->authorise('core.delete', 'com_passepartout.category.' . (int) $record->catid);
        }

        return parent::canDelete($record);
    }

    /**
     * A method to preprocess generating a new title in order to allow tables with alternative names
     * for alias and title to use the batch move and copy methods
     *
     * @param   integer  $categoryId  The target category id
     * @param   Table    $table       The \Joomla\CMS\Table\Table within which move or copy is taking place
     *
     * @return  void
     *
     * @since   3.8.12
     */
    public function generateTitle($categoryId, $table)
    {
        // Alter the title & alias
        $data = $this->generateNewTitle($categoryId, $table->alias, $table->name);
        $table->name = $data['0'];
        $table->alias = $data['1'];
    }

    /**
     * Method to test whether a record can have its state changed.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canEditState($record)
    {
        // Check against the category.
        if (!empty($record->catid)) {
            return $this->getCurrentUser()->authorise('core.edit.state', 'com_passepartout.category.' . (int) $record->catid);
        }

        // Default to component settings if category not known.
        return parent::canEditState($record);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form. [optional]
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. [optional]
     *
     * @return  Form|boolean  A Form object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_passepartout.document', 'document', ['control' => 'jform', 'load_data' => $loadData]); //  instructs the method to look for the document.xml file inside the forms folder of the backend

        if (empty($form)) {
            return false;
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data)) {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
        }

        // Don't allow to change the created_by user if not allowed to access com_users.
        if (!$this->getCurrentUser()->authorise('core.manage', 'com_users')) {
            $form->setFieldAttribute('created_by', 'filter', 'unset');
        }

        $app = Factory::getApplication();
        $id = $app->input->get('id', null, 'int');

        if (isset($id) && $id > 0) {
            $form->setFieldAttribute('file_upload', 'required', 'false');
            $form->setFieldAttribute('file_upload', 'label', 'COM_PASSEPARTOUT_FIELD_FILE_REPLACE_LABEL');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_passepartout.edit.document.data', []);

        if (empty($data)) {
            $data = $this->getItem();

            //if ($data->get('file_size')) {
            //    $file_size = HTMLHelper::_('number.bytes', Utility::getMaxUploadSize($data->get('file_size') . 'MB'));
            //    $data->set('file_size', $file_size); // Don't worry about replacing the value since it never will be written on the DB
            //}

            // Prime some default values.
            if ($this->getState('document.id') == 0) {
                $filters = (array) $app->getUserState('com_passepartout.documents.filter');
                $filterCatId = $filters['category_id'] ?? null;

                $data->set('catid', $app->getInput()->getInt('catid', $filterCatId));
            }
        }

        $this->preprocessData('com_passepartout.document', $data);

        return $data;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   Table  $table  A record object.
     *
     * @return  array  An array of conditions to add to ordering queries.
     *
     * @since   1.6
     */
    protected function getReorderConditions($table)
    {
        $db = $this->getDatabase();

        return [
            $db->quoteName('catid') . ' = ' . (int) $table->catid,
            $db->quoteName('state') . ' >= 0',
        ];
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param   Table  $table  A Table object.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function prepareTable($table)
    {
        $date = Factory::getDate();
        $user = $this->getCurrentUser();

        if (empty($table->id)) {
            // Set the values
            $table->created = $date->toSql();
            $table->created_by = $user->id;

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('MAX(' . $db->quoteName('ordering') . ')')
                    ->from($db->quoteName('#__passepartout_documents'));

                $db->setQuery($query);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            }
        } else {
            // Set the values
            $table->modified = $date->toSql();
            $table->modified_by = $user->id;
        }

        // Increment the content version number.
        $table->version++;
    }

    /**
     * Allows preprocessing of the Form object.
     *
     * @param   Form    $form   The form object
     * @param   array   $data   The data to be merged into the form object
     * @param   string  $group  The plugin group to be executed
     *
     * @return  void
     *
     * @since    3.6.1
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        if ($this->canCreateCategory()) {
            $form->setFieldAttribute('catid', 'allowAdd', 'true');

            // Add a prefix for categories created on the fly.
            $form->setFieldAttribute('catid', 'customPrefix', '#new#');
        }

        parent::preprocessForm($form, $data, $group);
    }

    /**
     * Method to delete document
     *
     * @param   array  $pks  Primary keys of logs
     *
     * @return  boolean
     *
     * @since   3.9.0
     */
    public function delete(&$pks)
    {
        $app = Factory::getApplication();
        $table = $this->getTable();

        foreach ($pks as $pk) {
            if ($table->load($pk)) {
                try {
                    File::delete($this->uploadFolder . $table->id . '-' . $table->file_uniqid);
                } catch (\RuntimeException $e) {
                    $app->enqueueMessage($e->getMessage(), 'warning');
                }
            }
        }

        return parent::delete($pks);
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data)
    {
        //@TODO: fix same alias error
        //@TODO: fix file data loss when in case of alias error (or other validation errors)
        //@TODO: Table::getInstance is deprecated. Replace it as soon as possible

        $app = Factory::getApplication();
        $input = $app->getInput();
        $table = Table::getInstance('DocumentTable');
        $files = $input->files->get('jform');
        $params = ComponentHelper::getParams('com_passepartout');

        // Create new category, if needed.
        $createCategory = true;

        // If category ID is provided, check if it's valid.
        if ($data['catid'] && is_numeric($data['catid'])) {
            $createCategory = !CategoriesHelper::validateCategoryId($data['catid'], 'com_passepartout');
        }

        // Save new category
        if ($createCategory && $this->canCreateCategory()) {
            $category = [
                // Remove #new# prefix, if exists.
                'title' => strpos($data['catid'], '#new#') === 0 ? substr($data['catid'], 5) : $data['catid'],
                'parent_id' => 1,
                'extension' => 'com_passepartout',
                'language' => $data['language'],
                'published' => 1,
            ];

            //@TODO: default category name is a number but it should be a string

            /** @var \Joomla\Component\Categories\Administrator\Model\CategoryModel $categoryModel */
            $categoryModel = Factory::getApplication()->bootComponent('com_categories')
                ->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]);

            // Create new category.
            if (!$categoryModel->save($category)) {
                $this->setError($categoryModel->getError());

                return false;
            }

            // Get the new category ID.
            $data['catid'] = $categoryModel->getState('category.id');
        }

        // Alter the name for save as copy
        if ($input->get('task') == 'save2copy') {
            /** @var \GiovanniMansillo\Component\Passepartout\Administrator\Table\DocumentTable $origTable */
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            if ($data['name'] == $origTable->name) {
                list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);
                $data['name'] = $name;
                $data['alias'] = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }

            $data['state'] = 0;
        }

        if ($input->get('task') == 'save2copy' || $input->get('task') == 'copy') {
            $data['clicks'] = 0;
            $data['impmade'] = 0;
        }

        // File management
        $file = $files['file_upload'];
        if (isset($file['size']) && $file['size'] > 0) {

            // @TODO: move validation in custom field validation
            if ($file['size'] > Utility::getMaxUploadSize()) {

                // @TODO: localize error message
                $error_message = 'File size exceeded maximum limit of ".' . Utility::getMaxUploadSize();
                $app->enqueueMessage($error_message, 'error');

                return false;
            }

            $blacklistedExtensionsParam = $params->get('blacklisted_extensions', '');
            $blacklistedExtensionsList = array_map('trim', explode(',', $blacklistedExtensionsParam));

            $extension = File::getExt($file['name']);
            if (in_array($extension, $blacklistedExtensionsList)) {

                // @TODO: localize error message
                $error_message = 'File extension ".' . $extension . '" not admitted';
                $app->enqueueMessage($error_message, 'error');

                return false;
            }

            // @TODO: Check max_upload_filesize and warn user - ()

            if (isset($file['error']) && !empty($file['error'])) {
                $app->enqueueMessage($file['error'], 'warning');
                return false;
            }

            // Ensure that a malicious user hasn't tried to trick the script into working on files upon which it should not be working--for instance, /etc/passwd.
            if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                // @TODO: localize error message
                $app->enqueueMessage("Error encountered uploading " . $file['tmp_name'], 'warning');
                return false;
            }

            $filePath = $this->uploadFolder . $data['id'] . '-' . $file['name'];

            // Delete previous file
            if (is_file($filePath) && !File::delete($filePath)) {
                // @TODO:  localize warning message
                // @TODO:  seems that it's never thrown. Instead an exception is thrown
                $app->enqueueMessage("Unable to delete previous file. Remove it manually from " . $filePath, 'warning');
            }

            // File upload
            $data['file'] = json_encode((object) [
                'name' => File::makeSafe($file['name']),
                'size' => $file['size'],
                'md5' => md5_file($file["tmp_name"])
            ]);

            if (!File::upload($file["tmp_name"], $filePath)) {
                // @TODO: localize error message
                $app->enqueueMessage("Error encountered moving " . $file['tmp_name'] . " into " . $filePath, 'error');
                return false;
            }
        }

        return parent::save($data);
    }

    /**
     * Is the user allowed to create an on the fly category?
     *
     * @return  boolean
     *
     * @since   3.6.1
     */
    private function canCreateCategory()
    {
        return $this->getCurrentUser()->authorise('core.create', 'com_passepartout');
    }

}
