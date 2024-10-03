<?php

/**
 * @package     GiovanniMansillo.Dory
 * @subpackage  com_dory
 *
 * @copyright   2024 Giovanni Mansillo <https://www.gmansillo.it>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GiovanniMansillo\Component\Dory\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Documents component helper.
 *
 * @since  1.6
 */
class DocumentsHelper extends ContentHelper
{
    /**
     * Update / reset the documents
     *
     * @return  boolean
     *
     * @since   1.6
     */
    public static function updateReset()
    {
        $db      = Factory::getDbo();
        $nowDate = Factory::getDate()->toSql();
        $app     = Factory::getApplication();
        $user    = $app->getIdentity();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__dory_documents'))
            ->where(
                [
                    $db->quoteName('reset') . ' <= :date',
                    $db->quoteName('reset') . ' IS NOT NULL',
                ]
            )
            ->bind(':date', $nowDate)
            ->extendWhere(
                'AND',
                [
                    $db->quoteName('checked_out') . ' IS NULL',
                    $db->quoteName('checked_out') . ' = :userId',
                ],
                'OR'
            )
            ->bind(':userId', $user->id, ParameterType::INTEGER);

        $db->setQuery($query);

        try {
            $rows = $db->loadObjectList();
        } catch (\RuntimeException $e) {
            $app->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        foreach ($rows as $row) {
            $purchaseType = $row->purchase_type;

            if ($purchaseType < 0 && $row->cid) {
                /** @var \GiovanniMansillo\Component\Dory\Administrator\Table\ClientTable $client */
                $client = Table::getInstance('ClientTable', '\\GiovanniMansillo\\Component\\Dory\\Administrator\\Table\\');
                $client->load($row->cid);
                $purchaseType = $client->purchase_type;
            }

            if ($purchaseType < 0) {
                $params       = ComponentHelper::getParams('com_dory');
                $purchaseType = $params->get('purchase_type');
            }

            switch ($purchaseType) {
                case 1:
                    $reset = null;
                    break;
                case 2:
                    $date  = Factory::getDate('+1 year ' . date('Y-m-d'));
                    $reset = $date->toSql();
                    break;
                case 3:
                    $date  = Factory::getDate('+1 month ' . date('Y-m-d'));
                    $reset = $date->toSql();
                    break;
                case 4:
                    $date  = Factory::getDate('+7 day ' . date('Y-m-d'));
                    $reset = $date->toSql();
                    break;
                case 5:
                    $date  = Factory::getDate('+1 day ' . date('Y-m-d'));
                    $reset = $date->toSql();
                    break;
            }

            // Update the row ordering field.
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__dory_documents'))
                ->set(
                    [
                        $db->quoteName('reset') . ' = :reset',
                        $db->quoteName('impmade') . ' = 0',
                        $db->quoteName('clicks') . ' = 0',
                    ]
                )
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':reset', $reset, $reset === null ? ParameterType::NULL : ParameterType::STRING)
                ->bind(':id', $row->id, ParameterType::INTEGER);

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (\RuntimeException $e) {
                $app->enqueueMessage($e->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    public static function getAccessLevels()
    {
        return [
            1 => Text::_('COM_DORY_FIELD_ACCESS_OPTION_PUBLIC_VALUE'),
            2 => Text::_('COM_DORY_FIELD_ACCESS_OPTION_USERS_VALUE'),
            3 => Text::_('COM_DORY_FIELD_ACCESS_OPTION_USERGROUPS_VALUE'),
        ];
    }
}
