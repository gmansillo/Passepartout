<?php

/**
 * @package     GiovanniMansillo.Passepartout
 * @subpackage  com_passepartout
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

/** @var \GiovanniMansillo\Component\Passepartout\Administrator\View\Documents\HtmlView $this */

$displayData = [
    'textPrefix' => 'COM_PASSEPARTOUT',
    'formURL'    => 'index.php?option=com_passepartout&view=documents',
    'icon'       => 'icon-file passepartout',
];

$user = $this->getCurrentUser();

if ($user->authorise('core.create', 'com_passepartout') || count($user->getAuthorisedCategories('com_passepartout', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_passepartout&task=document.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
