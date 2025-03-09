<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip', '.hastooltip', ['html' => true, 'placement' => 'top']);

defined('_JEXEC') or die;

extract($displayData);

$app = Factory::getApplication();
$id = $app->input->get('id', null, 'int');

$value_decoded = json_decode($value);

$tooltipText = array();


?>

<?php if ($value_decoded && $value_decoded->name): ?>

    <?php
    if ($value_decoded) {
        if ($value_decoded->md5)
            $tooltipText[] = Text::_('COM_PASSEPARTOUT_FIELD_FILE_MD5_LABEL') . " <strong> " . $value_decoded->md5 . "</strong>";
        if ($value_decoded->size)
            $tooltipText[] = Text::_('COM_PASSEPARTOUT_FIELD_FILE_SIZE_LABEL') . " <strong> " . HTMLHelper::_('number.bytes', Utility::getMaxUploadSize($value_decoded->size . 'MB')) . "</strong>";
    }
    ?>

    <div class="input-group hastooltip" title="<?= join(" - ", $tooltipText) ?>">
        <input
                class="form-control"
                disabled
                type="text"
                value="<?php echo htmlspecialchars($value_decoded->name ?? $value, ENT_COMPAT, 'UTF-8'); ?>"
                readonly="readonly"/>

        <a href="./index.php?option=com_passepartout&view=document&id=<?= $id; ?>&format=raw" target="_blank"
           class="btn btn-primary"></a>
    </div>
<?php endif; ?>

<input
        type="hidden"
        name="<?php echo $name; ?>"
        id="<?php echo $id; ?>"
        value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"/>