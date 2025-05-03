<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Manticore implements ParserInterface
{
    /**
     * @return array{
     *     global_variables: array<string, string>,
     *     status: array<string, string>,
     *     settings: array<string, string>,
     *     agent_status: array<string, string>,
     * }|null
     */
    public function run(\PDO $connection = new \PDO('mysql:host=127.0.0.1;port=9306', 'root', '')): ?array
    {
        $result = [
            'global_variables' => [],
            'status' => [],
            'settings' => [],
            'agent_status' => [],
        ];

        $query = $connection->query('SHOW GLOBAL VARIABLES');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['global_variables'][$row['Variable_name']] = $row['Value'];
            }
        }

        $query = $connection->query('SHOW STATUS');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['status'][$row['Counter']] = $row['Value'];
            }
        }

        $query = $connection->query('SHOW SETTINGS');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['settings'][$row['Setting_name']] = $row['Value'];
            }
        }

        $query = $connection->query('SHOW AGENT STATUS');
        if ($query) {
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $result['agent_status'][$row['Key']] = $row['Value'];
            }
        }

        return $result;
    }
}
