<?php

namespace DomacinskiBurek\System\Query;

use Exception;
use DomacinskiBurek\System\Query\Interfaces\QueryInterface;

class InsertQuery implements QueryInterface
{
    private array $queryBox = [
        "create_new_user"                   => "INSERT INTO mt_user (uuid, first_name, last_name, email, username, password, dateofbirth) VALUES (?,?,?,?,?,?,?)",
        "create_new_user_verify"            => "INSERT INTO mt_user_verification (uuid, u_id, status) VALUES (?,?,?)",
        "create_new_user_session"           => "INSERT INTO mt_user_sessions (uuid, u_id) VALUES (?,?)",
        "create_new_reset_password"         => "INSERT INTO mt_user_reset_password (uuid, u_id) VALUES (?,?)",
        "create_school_major"               => "INSERT INTO mt_school_major (uuid, name, description) VALUES (?,?,?)",
        "create_school_major_link"          => "INSERT INTO mt_school_major_link (s_id, m_id) VALUES (?,?)",
        "create_school_profile"             => "INSERT INTO mt_school_user (uuid, u_id, first_name, last_name, gender, biography, role, graduated) VALUES (?,?,?,?,?,?,?,?)",
        "create_school_profile_link"        => "INSERT INTO mt_school_user_link (s_id, sc_id) VALUES (?,?)",
        "create_school_class"               => "INSERT INTO mt_school_class (uuid, name, description) VALUES (?,?,?)",
        "create_school_class_link"          => "INSERT INTO mt_school_class_link (m_id, c_id) VALUES (?,?)",
        "create_school_user_gallery"        => "INSERT INTO mt_school_user_gallery (uuid, u_id, image_name, is_primary) VALUES (?,?,?,?)",
        "create_school_class_gallery"       => "INSERT INTO mt_school_class_gallery (uuid, sc_id, image_name) VALUES (?,?,?)",
        "create_school_draft_profile"       => "INSERT INTO mt_school_draft_user (uuid, first_name, last_name, gender, biography, role, graduated, city_id, school_id, major_id, class_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
        "create_school_draft_user_gallery"  => "INSERT INTO mt_school_draft_user_gallery (uuid, u_id, image_name, is_primary) VALUES (?,?,?,?)",
    ];

    /**
     * @throws Exception
     */
    public function get(string $query) : string
    {
        if (array_key_exists($query, $this->queryBox) === false) throw new Exception('query does not exist in our system.');

        return $this->queryBox[$query];
    }
}