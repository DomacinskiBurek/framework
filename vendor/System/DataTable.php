<?php

namespace DomacinskiBurek\System;

use DomacinskiBurek\System\Error\Handlers\DataTableColumnNotFound;
use DomacinskiBurek\System\Error\Handlers\DataTableEmptyColumns;
use DomacinskiBurek\System\Error\Handlers\DataTableInvalidRequest;

class DataTable
{
    private array $columns = [];
    private array $request = [];

    public function setColumns (array $columns): DataTable
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @throws DataTableInvalidRequest
     */
    public function setRequest (array $request): DataTable
    {
        if ($this->validation($request) === false) throw new DataTableInvalidRequest('Invalid Request');

        $this->request = $request;

        return $this;
    }

    /**
     * @throws DataTableEmptyColumns
     * @throws DataTableColumnNotFound
     */
    public function createRequest (): object
    {
        if (empty($this->columns)) throw new DataTableEmptyColumns();

        return (object) ['columns' => implode(",", $this->columns), 'search' => $this->search(), 'order' => $this->order(), 'page' => $this->page(), 'limit' => $this->limit(), 'draw' => $this->request['draw']];
    }

    protected function page (): int
    {
        $start = $this->request['start'];
        return $start;//$this->limit();
    }

    protected function limit (): int
    {
        return $this->request['length'];
    }
    /**
     * @throws DataTableColumnNotFound
     */
    protected function search (): string
    {
        $search       = [];
        $columnSearch = [];

        if (array_key_exists('value', $this->request['search']) && !empty($this->request['search']['value'])) {
            foreach ($this->request['columns'] as $object) {
                $column = $this->getColumnName($object['data']);

                if ($object['searchable'] === "false") continue;

                $search[] = "{$this->columns[$column['data']]} LIKE '%{$this->request['search']['value']}%'";

                if (empty($object['search']['value'])) continue;

                $columnSearch[] = "{$this->columns[$column['data']]} LIKE '%{$this->request['search']['value']}%'";
            }
        }

        $wherePattern = "";

        if (count($search) > 0) $wherePattern = "(" . implode(' OR ', $search) . ")";
        if (count($columnSearch) > 0) $wherePattern = ($wherePattern === '') ? implode(' AND ', $columnSearch) : "$wherePattern AND " . implode(' AND ', $columnSearch);

        if (!empty($wherePattern)) $wherePattern = "WHERE $wherePattern";

        return $wherePattern;
    }

    /**
     * @throws DataTableColumnNotFound
     */
    protected function getColumnName (int $columnPosition)
    {
        if (array_key_exists($columnPosition, $this->request['columns']) === false) throw new DataTableColumnNotFound();

        return $this->request['columns'][$columnPosition];
    }

    /**
     * @throws DataTableColumnNotFound
     */
    protected function order (): string
    {
        $orderBy = [];

        if (count($this->request['order']) > 0) {
            foreach ($this->request['order'] as $object) {
                $column = $this->getColumnName($object['column']);

                if ($column['orderable'] === false) continue;

                $orderBy[] = "{$this->columns[$column['data']]} {$object['dir']}";
            }
        }

        if (count($orderBy) > 0) $orderBy = implode(', ', $orderBy);

        return (empty($orderBy)) ? "" : $orderBy;
    }

    private function validation (array $request): bool
    {
        $properties  = ['draw', 'columns', 'order', 'start', 'length', 'search'];

        foreach ($properties as $property) {
            if (array_key_exists($property, $request) === false) return false;

            if ($this->objectValidation($property, $request[$property]) === false) return false;
        }

        return true;
    }

    private function objectValidation ($property, $object): bool
    {
        switch ($property) {
            case 'search':
                foreach (['value', 'regex'] as $key) if (array_key_exists($key, $object) === false) return false;
                return true;
            case 'order':
                foreach ($object as $item) foreach (['column', 'dir'] as $key) if (array_key_exists($key, $item) === false) return false;
                return true;
            case 'columns':
                foreach ($object as $item) {
                    foreach (['data', 'name', 'searchable', 'orderable', 'search'] as $key) {
                        if (array_key_exists($key, $item) && $key === 'search') {
                            if ($this->objectValidation('search', $item['search']) === false) return false;
                        } else if (array_key_exists($key, $item) === false) {
                            return false;
                        }
                    }
                }
                return true;
            default:
                return true;
        }
    }
}