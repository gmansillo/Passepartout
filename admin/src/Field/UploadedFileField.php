<?php

namespace GiovanniMansillo\Component\Dory\Administrator\Field;

use Joomla\CMS\Form\Field\TextField;


class UploadedFileField extends TextField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $type = 'uploadedfile';

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  3.7
     */
    protected $layout = 'uploadedfile';
}
