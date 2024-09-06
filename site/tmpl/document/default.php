<?php
defined('_JEXEC');
?>
<div class="document-item p-4">
    <h1><?php echo $this->item->title; ?></h1>
    <div id="created" class="date meta">
        <?php echo $this->item->created; ?>
    </div>
    <p id="description" class="description">
        <?php echo $this->item->description; ?>
    </p>
    <div id="id" class="date">
        <?php echo $this->item->id; ?>
    </div>
</div>