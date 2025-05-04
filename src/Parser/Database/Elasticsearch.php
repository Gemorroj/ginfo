<?php

namespace Ginfo\Parser\Database;

use Ginfo\Parser\ParserInterface;

final readonly class Elasticsearch implements ParserInterface
{
    /**
     * @param string $statsPage uri for json stats page https://localhost:9200/_cluster/stats for example. see https://www.elastic.co/docs/api/doc/elasticsearch/operation/operation-cluster-stats
     *
     * @return array{
     *     stats: array{
     *         cluster_name: string,
     *         cluster_uuid: string,
     *         timestamp: int,
     *         status: string,
     *         nodes: array{
     *             count: int,
     *             versions: string[],
     *             available_processors: int,
     *             allocated_processors: int,
     *             mem_total_in_bytes: int|float,
     *             mem_free_in_bytes: int|float,
     *             mem_used_in_bytes: int|float,
     *             mem_free_percent: int,
     *             mem_used_percent: int,
     *             process_cpu_percent: int,
     *             process_open_file_descriptors_min: int,
     *             process_open_file_descriptors_max: int,
     *             process_open_file_descriptors_avg: int,
     *             jvm_versions: string[],
     *             jvm_mem_heap_used_in_bytes: int|float,
     *             jvm_mem_heap_max_in_bytes: int|float,
     *             jvm_threads: int,
     *             fs_total_in_bytes: int|float,
     *             fs_free_in_bytes: int|float,
     *             fs_available_in_bytes: int|float,
     *             fs_cache_reserved_in_bytes: int|float,
     *             plugins: string[],
     *         },
     *         indices: array{
     *             count: int,
     *             store_size_in_bytes: int|float,
     *             store_reserved_in_bytes: int|float,
     *             fielddata_memory_size_in_bytes: int|float,
     *             fielddata_evictions: int|float,
     *             query_cache_memory_size_in_bytes: int|float,
     *             query_cache_total_count: int|float,
     *             query_cache_hit_count: int|float,
     *             query_cache_miss_count: int|float,
     *             query_cache_cache_size: int|float,
     *             query_cache_cache_count: int|float,
     *             query_cache_evictions: int|float,
     *             completion_size_in_bytes: int|float,
     *             segments_count: int|float,
     *             segments_memory_in_bytes: int|float,
     *         },
     *     }
     * }|null
     */
    public function run(string $statsPage = 'https://localhost:9200/_cluster/stats', ?string $username = null, ?string $password = null): ?array
    {
        $result = [
            'stats' => [],
        ];

        $context = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
        if ($username || $password) {
            $context['http'] = [
                'header' => 'Authorization: Basic '.\base64_encode($username.':'.$password),
            ];
        }

        $stats = \file_get_contents($statsPage, false, \stream_context_create($context));
        $json = \json_decode($stats, true, 512, \JSON_THROW_ON_ERROR);

        $result['stats'] = [
            'cluster_name' => $json['cluster_name'],
            'cluster_uuid' => $json['cluster_uuid'],
            'timestamp' => $json['timestamp'],
            'status' => $json['status'],
            'nodes' => [
                'count' => $json['nodes']['count']['total'],
                'versions' => $json['nodes']['versions'],
                'available_processors' => $json['nodes']['os']['available_processors'],
                'allocated_processors' => $json['nodes']['os']['allocated_processors'],
                'mem_total_in_bytes' => $json['nodes']['os']['mem']['total_in_bytes'],
                'mem_free_in_bytes' => $json['nodes']['os']['mem']['free_in_bytes'],
                'mem_used_in_bytes' => $json['nodes']['os']['mem']['used_in_bytes'],
                'mem_free_percent' => $json['nodes']['os']['mem']['free_percent'],
                'mem_used_percent' => $json['nodes']['os']['mem']['used_percent'],
                'process_cpu_percent' => $json['nodes']['process']['cpu']['percent'],
                'process_open_file_descriptors_min' => $json['nodes']['process']['open_file_descriptors']['min'],
                'process_open_file_descriptors_max' => $json['nodes']['process']['open_file_descriptors']['max'],
                'process_open_file_descriptors_avg' => $json['nodes']['process']['open_file_descriptors']['avg'],
                'jvm_versions' => \array_map(static function (array $version): string {
                    return $version['version'];
                }, $json['nodes']['jvm']['versions']),
                'jvm_mem_heap_used_in_bytes' => $json['nodes']['jvm']['mem']['heap_used_in_bytes'],
                'jvm_mem_heap_max_in_bytes' => $json['nodes']['jvm']['mem']['heap_max_in_bytes'],
                'jvm_threads' => $json['nodes']['jvm']['threads'],
                'fs_total_in_bytes' => $json['nodes']['fs']['total_in_bytes'],
                'fs_free_in_bytes' => $json['nodes']['fs']['free_in_bytes'],
                'fs_available_in_bytes' => $json['nodes']['fs']['available_in_bytes'],
                'fs_cache_reserved_in_bytes' => $json['nodes']['fs']['cache_reserved_in_bytes'],
                'plugins' => \array_map(static function (array $plugin): string {
                    return $plugin['name'];
                }, $json['nodes']['plugins']),
            ],
            'indices' => [
                'count' => $json['indices']['count'],
                'store_size_in_bytes' => $json['indices']['store']['size_in_bytes'],
                'store_reserved_in_bytes' => $json['indices']['store']['reserved_in_bytes'],
                'fielddata_memory_size_in_bytes' => $json['indices']['fielddata']['memory_size_in_bytes'],
                'fielddata_evictions' => $json['indices']['fielddata']['evictions'],
                'query_cache_memory_size_in_bytes' => $json['indices']['query_cache']['memory_size_in_bytes'],
                'query_cache_total_count' => $json['indices']['query_cache']['total_count'],
                'query_cache_hit_count' => $json['indices']['query_cache']['hit_count'],
                'query_cache_miss_count' => $json['indices']['query_cache']['miss_count'],
                'query_cache_cache_size' => $json['indices']['query_cache']['cache_size'],
                'query_cache_cache_count' => $json['indices']['query_cache']['cache_count'],
                'query_cache_evictions' => $json['indices']['query_cache']['evictions'],
                'completion_size_in_bytes' => $json['indices']['completion']['size_in_bytes'],
                'segments_count' => $json['indices']['segments']['count'],
                'segments_memory_in_bytes' => $json['indices']['segments']['memory_in_bytes'],
            ],
        ];

        return $result;
    }
}
