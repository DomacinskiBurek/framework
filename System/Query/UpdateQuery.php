<?php

namespace DomacinskiBurek\System\Query;

use Exception;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;

class UpdateQuery implements QueryInterface
{
    private array $queryBox = [
        "update_verify_key"        => "UPDATE mt_user_verification mmv, mt_user mu SET mmv.status = true, mmv.updated_at = NOW(), mu.is_verified = true, mu.updated_at = NOW() WHERE mmv.u_id = mu.id AND mmv.uuid = ?",
        "update_user_password"     => "UPDATE mt_user SET password = ? WHERE id = ?",
        "update_language_locale"   => "UPDATE mt_user SET locale = (SELECT id FROM mt_language WHERE locale = ?) WHERE id = ?",
        "update_personal_profile"  => "UPDATE mt_user SET first_name = ?, last_name = ?, password = %s, email = ?, phone = ?, dateofbirth = ? WHERE id = ?",
        "update_linked_school_profile" => "UPDATE mt_school_user SET first_name = ?, last_name = ? WHERE u_id = ?",
        "update_user_avatar"       => "UPDATE mt_user SET avatar = ? WHERE id = ?",
        "update_school_major"      => "UPDATE mt_school_major msm, mt_school_major_link msml SET msm.name = ?, msm.description = ?, msml.s_id = ? WHERE msm.id = msml.m_id AND msm.uuid = ?",
        "update_school_class"      => "UPDATE mt_school_class msc, mt_school_class_link mscl SET msc.name = ?, msc.description = ?, mscl.m_id = ? WHERE msc.id = mscl.c_id AND msc.uuid = ?",
        "update_school_user_with_link" => "UPDATE mt_school_user msu, mt_school_user_link msul SET msu.first_name = :first_name, msu.last_name = :last_name, msu.gender = :user_gender, msu.role = :user_role, msu.biography = :user_biography, msu.graduated = :user_generation, msul.sc_id = :class_id WHERE msu.id = msul.s_id AND msu.uuid = :user_uuid",
        "update_school_user"       => "UPDATE mt_school_user msu SET msu.first_name = :first_name, msu.last_name = :last_name, msu.gender = :user_gender, msu.role = :user_role, msu.biography = :user_biography, msu.graduated = :user_generation WHERE msu.uuid = :user_uuid",
        "update_school_draft_user" => "UPDATE mt_school_draft_user SET status = 1 WHERE uuid = ?",
        "update_user_verify"       => "UPDATE mt_school_user SET is_verified = 1, verified_by = ?, verified_note = ? WHERE uuid = ?",
        "update_major_verify"      => "UPDATE mt_school_major SET is_verified = 1, verified_by = ?, verified_note = ? WHERE uuid = ?",
        "update_class_verify"      => "UPDATE mt_school_class SET is_verified = 1, verified_by = ?, verified_note = ? WHERE uuid = ?"
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