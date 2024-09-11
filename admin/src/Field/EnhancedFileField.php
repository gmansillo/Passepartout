<?php

namespace GiovanniMansillo\Component\Dory\Administrator\Field;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\FileField;
use Joomla\Registry\Registry;

class EnhancedFileField extends FileField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $type = 'enhancedfile';

    /**
     * Method to validate a FileField object based on field data.
     *
     * @param   mixed      $value  The optional value to use as the default for the field.
     * @param   string     $group  The optional dot-separated form group path on which to find the field.
     * @param   ?Registry  $input  An optional Registry object with the entire data set to validate
     *                             against the entire form.
     *
     * @return  boolean|\Exception  Boolean true if field value is valid, Exception on failure.
     *
     * @since   4.0.0
     * @throws  \InvalidArgumentException
     * @throws  \UnexpectedValueException
     */
    public function validate($value, $group = null, Registry $input = null)
    {
        $files = Factory::getApplication()->getInput()->files->get('jform');
        $file = $files[(string) $this->element->attributes()->{'name'}];

        $required = ((string) $this->element['required'] === 'true' || (string) $this->element['required'] === 'required');
        $requiredValue = $this->element['required'];

        // If a file has been submitted, bypass joomla required validation setting required to false 
        if ($required && isset($file) && file_exists($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            $this->element['required'] = 'false';
        }

        // Invoke standard joomla validation
        $valid = parent::validate($value, $group, $input);

        // Restore initial value for required attribute
        $this->element['required'] = $required;

        return $valid;
    }
}
