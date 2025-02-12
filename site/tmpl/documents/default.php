<?php
\defined('_JEXEC');

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

$wam = Factory::getApplication()->getDocument()->getWebAssetManager();
$wam->useStyle('com_passepartout.documents');

?>

<form>
    <div class="items-limit-box">
        <?php echo $this->pagination->getLimitBox(); ?>
    </div>
</form>

<div class="cards row row-col-3">
    <?php foreach ($this->items as $item) : ?>
        <div class="card col m-1">
            <h2>
                <?php echo $item->title; ?>
            </h2>
            <div id="document-id">
                <?php echo $item->id; ?>
            </div>
            <div id="document-file_size">
                <?php echo $item->file_size; ?> byte
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div>
    <?php echo $this->pagination->getResultsCounter(); ?>
</div>

<?php echo $this->pagination->getListFooter(); ?>

<input type="hidden" name="task" value="documents">

<?php echo HTMLHelper::_('form.token'); ?>