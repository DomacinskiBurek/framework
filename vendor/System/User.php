<?php

namespace DomacinskiBurek\System;

use Exception;
use DomacinskiBurek\System\Error\Handlers\DatabaseException;
use PDO;
use DomacinskiBurek\System\Error\Handlers\DatabaseNotFound;

class User
{
    protected Session $session;
    private ?array $details = null;
    /**
     * @throws Exception
     */
    function __construct ()
    {
        $this->session = new Session();
    }

    /**
     * @throws Exception
     */
    public function isLogged ()
    {
        if ($this->session->has('UserLogin') === false) return false;

        $sUid     = $this->session->get('UserLogin');
        $database = Database::connect('database');

        $prepare  = $database->prepare(Query::generate('Select::exist_user_session_check'));
        $prepare->execute([$sUid]);

        $fetch_sql = $prepare->fetch(PDO::FETCH_OBJ);

        if (empty($fetch_sql)) {
            $this->session->delete("UserLogin");
            return false;
        }

        return $fetch_sql->u_id;
    }


    /**
     * @throws DatabaseException
     * @throws Exception
     */
    public function UserDetails (int $userId)
    {
        $database    = Database::connect('database');

        if (is_null($this->details) === false) return $this->details;

        $prepare_sql = $database->prepare(Query::generate("Select::user_details"));
        $prepare_sql->execute([$userId]);

        return $prepare_sql->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @throws DatabaseException
     * @throws Exception
     */
    public function UserAccess (int $userId): object
    {
        $database = Database::connect('database');

        $access = (object) ["module" => [], "page" => [], "sub_page" => []];
        // Module list
        $prepare_sql = $database->prepare(Query::generate("Select::user_module_access_list"));
        $prepare_sql->execute([$userId]);

        return (object) [
            "access_to" => $access,
            "menu_tree" => array_map(
                function ($module) use ($userId, &$database, &$access) {
                    $access->module[] = $module->id;
                    return [
                        "type"  => "module",
                        "id"    => $module->id,
                        "name"  => language("Misc.$module->translate_tag"),
                        "route" => $module->route,
                        "translate_tag" => $module->translate_tag,
                        "is_hidden"     => $module->is_hidden,
                        "tree"  => (function ($module, $userId) use (&$database, &$access) {
                            $prepare_sql = $database->prepare(Query::generate("Select::user_page_access_list"));
                            $prepare_sql->execute([$userId, $module]);

                            return array_map(
                                function ($page) use ($module, $userId, &$database, &$access) {
                                    $access->page[] = $page->id;
                                    return [
                                        "type"  => "page",
                                        "id"    => $page->id,
                                        "name"  => language("Misc.$page->translate_tag"),
                                        "route" => $page->route,
                                        "icon"  => $page->icon,
                                        "translate_tag" => $page->translate_tag,
                                        "is_hidden"     => $page->is_hidden,
                                        "tree"  => (function ($module, $page, $userId) use (&$database, &$access) {
                                            $prepare_sql = $database->prepare(Query::generate("Select::user_sub_page_access_list"));
                                            $prepare_sql->execute([$userId, $module, $page]);

                                            return array_map(
                                                function ($sub_page) use (&$access) {
                                                    $access->sub_page[] = $sub_page->id;
                                                    return [
                                                        "type"  => "sub_page",
                                                        "id"    => $sub_page->id,
                                                        "name"  => language("Misc.$sub_page->translate_tag"),
                                                        "route" => $sub_page->route,
                                                        "icon"  => $sub_page->icon,
                                                        "translate_tag" => $sub_page->translate_tag,
                                                        "is_hidden"     => $sub_page->is_hidden,
                                                        "tree"  => []
                                                    ];
                                                }, $prepare_sql->fetchAll(PDO::FETCH_OBJ)
                                            );
                                        })($module, $page->id, $userId)
                                    ];
                                }
                            , $prepare_sql->fetchAll(PDO::FETCH_OBJ));
                        })($module->id, $userId)
                    ];
                }
            , $prepare_sql->fetchAll(PDO::FETCH_OBJ))
        ];
    }
}