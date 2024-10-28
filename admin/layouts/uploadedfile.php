<?php

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

extract($displayData);

$app        =   Factory::getApplication();
$id         =   $app->input->get('id', null, 'int');

?>

<input
    type="hidden"
    name="<?php echo $name; ?>"
    id="<?php echo $id; ?>"
    value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>">

<div class="input-group">
    <input
        class="form-control"
        disabled
        type="text"
        value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"
        readonly="readonly">

    <?php if ($value): ?>
        <a href="./index.php?option=com_dory&view=document&id=<?= $id; ?>&format=raw" target="_blank" class="btn btn-primary"> </a>
    <?php endif; ?>

</div>