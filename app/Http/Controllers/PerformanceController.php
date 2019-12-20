<?php


namespace App\Http\Controllers;


use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Support\Arr;

/**
 * Class PerformanceController
 * @package App\Http\Controllers
 */
class PerformanceController
{
    /**
     *
     * @OA\Get(
     *     path="/kpideclientes",
     *     tags={"performance"},
     *     description="Promedio y desviación estándar de las edades de los clientes",
     *
     *     @OA\Response(response="200", description="KPIs de todos los clientes")
     * )
     *
     * @param DynamoDbClient $dynamoDbClient
     * @return array
     */
    public function index(DynamoDbClient $dynamoDbClient)
    {
        $kpi = $dynamoDbClient->getItem([
            'Key' => [
                'id' => [
                    'S' => 'kpi_clients_version_1',
                ],
                'type' => [
                    'S' => 'kpi',
                ],
            ],
            'TableName' => 'in_digital_challenge',
        ]);

        $data = $kpi->get('Item');

        if (!$data) {
          return [
              'promedio' => 0,
              'desviacion_estandar' => 0,
          ];
        }

        return [
            'promedio' => Arr::get($data, 'average.N'),
            'desviacion_estandar' => sqrt(Arr::get($data, 'variance.N')),
        ];
    }
}
