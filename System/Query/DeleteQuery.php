<?php

namespace DomacinskiBurek\System\Query;

use Exception;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;

class DeleteQuery implements QueryInterface
{

    private array $queryBox = [
        "user_session_delete"              => "DELETE FROM mt_user_sessions WHERE uuid = ?",
        "user_reset_password_delete"       => "DELETE FROM mt_user_reset_password WHERE u_id = ?",
        "user_reset_password_delete_uuid"  => "DELETE FROM mt_user_reset_password WHERE uuid = ?",
        "user_verification_request_delete" => "DELETE FROM mt_user_verification WHERE u_id = ?",
        "school_school_user_image_remove"       => "DELETE msug FROM mt_school_user_gallery msug WHERE msug.uuid = ? AND EXISTS(SELECT 1 FROM mt_school_user AS msu WHERE msug.u_id = msu.id AND msu.uuid = ?)",
        "school_school_draft_user_image_remove" => "DELETE msdug FROM mt_school_draft_user_gallery msdug WHERE msdug.uuid = ? AND EXISTS(SELECT 1 FROM mt_school_draft_user AS msdu WHERE msdug.u_id = msdu.id AND msdu.uuid = ?)",
        "school_school_class_image_remove"      => "DELETE mscg FROM mt_school_class_gallery mscg WHERE mscg.uuid = ? AND EXISTS(SELECT 1 FROM mt_school_class AS msc WHERE msc.uuid = ? AND msc.id = mscg.sc_id)"
    ];

    /**
     * @throws Exception
     */
    public function build(string $query, ?array $params): string
    {
        return (is_null($params)) ? $this->get($query) : sprintf($this->get($query), ...$params);
    }

    /**
     * @throws Exception
     */
    private function get(string $query) : string
    {
        if (array_key_exists($query, $this->queryBox) === false) throw new Exception('Query does not exist in our system.');

        return $this->queryBox[$query];
    }
}