<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\Common;
use PhpMyAdmin\Database\Triggers;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Response;
use PhpMyAdmin\Template;
use PhpMyAdmin\Util;
use function in_array;
use function strlen;

/**
 * Triggers management.
 */
class TriggersController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    /**
     * @param Response          $response
     * @param string            $db       Database name.
     * @param string            $table    Table name.
     * @param DatabaseInterface $dbi
     */
    public function __construct($response, Template $template, $db, $table, $dbi)
    {
        parent::__construct($response, $template, $db, $table);
        $this->dbi = $dbi;
    }

    public function index(): void
    {
        global $db, $table, $tables, $num_tables, $total_num_tables, $sub_part, $is_show_stats;
        global $db_is_system_schema, $tooltip_truename, $tooltip_aliasname, $pos;
        global $errors;

        if (! $this->response->isAjax()) {
            /**
             * Displays the header and tabs
             */
            if (! empty($table) && in_array($table, $this->dbi->getTables($db))) {
                Common::table();
            } else {
                $table = '';
                Common::database();

                [
                    $tables,
                    $num_tables,
                    $total_num_tables,
                    $sub_part,
                    $is_show_stats,
                    $db_is_system_schema,
                    $tooltip_truename,
                    $tooltip_aliasname,
                    $pos,
                ] = Util::getDbInfo($db, $sub_part ?? '');
            }
        } elseif (strlen($db) > 0) {
            $this->dbi->selectDb($db);
        }

        /**
         * Keep a list of errors that occurred while
         * processing an 'Add' or 'Edit' operation.
         */
        $errors = [];

        $triggers = new Triggers($this->dbi, $this->template, $this->response);
        $triggers->main();
    }
}
