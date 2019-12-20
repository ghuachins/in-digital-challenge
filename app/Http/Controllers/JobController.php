<?php


namespace App\Http\Controllers;

use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Support\Arr;

/**
 * Class JobController
 *
 * @package App\Http\Controllers
 */
class JobController
{
    /**
     * @OA\Get(
     *     path="/job/kpi",
     *     tags={"jobs"},
     *     description="Resolver los KPIs",
     *
     *     @OA\Response(response="200", description="Actualización de los KPIs")
     * )
     * @param DynamoDbClient $dynamoDbClient
     * @return array
     */
    public function kpi(DynamoDbClient $dynamoDbClient)
    {

        $clients = $dynamoDbClient->scan([
            'ProjectionExpression' => '#t, age',
            'ExpressionAttributeNames' => [
                '#t' => 'type'
            ],
            'TableName' => 'in_digital_challenge',
        ]);

        $data = $clients->get('Items');

        $lastEvaluatedKey = $clients->get('LastEvaluatedKey');

        while (!empty($lastEvaluatedKey)) {
            $paginatedClients = $dynamoDbClient->scan([
                'ExclusiveStartKey' => $lastEvaluatedKey,
                'ProjectionExpression' => '#t, age',
                'ExpressionAttributeNames' => [
                    '#t' => 'type'
                ],
                'TableName' => 'in_digital_challenge',
            ]);

            $data = array_merge($data, $paginatedClients->get('Items'));

            $lastEvaluatedKey = $paginatedClients->get('LastEvaluatedKey');
        }

        $total = 0;
        $count = 0;
        foreach ($data as $datum) {
            $age = Arr::get($datum, 'age.N', null);
            if ($age) {
                $total += $age;
                $count++;
            }
        }

        $average = $total/$count;

        $sum = 0;
        foreach ($data as $datum) {
            $age = Arr::get($datum, 'age.N', null);
            if ($age) {
                $sum += pow($age - $average, 2);
            }
        }

        $variance = $sum/$count;


        $result = $dynamoDbClient->putItem([
            'Item' => [
                'id' => [
                    'S' => 'kpi_clients_version_1',
                ],
                'type' => [
                    'S' => 'kpi',
                ],
                'average' => [
                    'N' => $average,
                ],
                'variance' => [
                    'N' => $variance,
                ],
                'total_clients' => [
                    'N' => (string) $count,
                ],
                'updated_at' => [
                    'N' => (string) time(),
                ],
            ],
            'TableName' => 'in_digital_challenge',
        ]);

        return $result->toArray();
    }


}
