<?php

use Joomla\CMS\Factory;
use GiovanniMansillo\Component\Dory\Site\Helper\DoryHelper;

defined('_JEXEC') or die;

extract($displayData);

$app        =   Factory::getApplication();
$id         =   $app->input->get('id', null, 'int');
$fileData   =   json_decode($value, false);

$attributes = [
    ' ',
    $onchange ? ' onchange="' . $onchange . '"' : '',
];

$addonAfterHtml  = '';
?>

<input
    type="hidden"
    name="<?php echo $name; ?>"
    id="<?php echo $id; ?>"
    value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>">
<?php if ($fileData->size > 0): ?>
    <div class="input-group">
        <?php if (!empty($addonBefore)) : ?>
            <?php echo $addonBeforeHtml; ?>
        <?php endif; ?>

        <input
            class="form-control"
            disabled
            type="text"
            value="<?php echo htmlspecialchars($fileData->name, ENT_COMPAT, 'UTF-8'); ?>"
            readonly="readonly">

        <a href="./index.php?option=com_dory&view=document&id=<?= $id; ?>&format=raw" target="_blank" class="btn btn-primary"></a>

    </div>

    <br>

    Size: <strong><?= DoryHelper::formatSizeUnits($fileData->size) ?></strong>

<?php endif; ?>