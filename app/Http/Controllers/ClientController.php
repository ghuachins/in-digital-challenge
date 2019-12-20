<?php


namespace App\Http\Controllers;


use Aws\DynamoDb\DynamoDbClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

/**
 * Class ClientController
 * @package App\Http\Controllers
 */
class ClientController extends Controller
{
    /**
     * @OA\Post(
     *     path="/creacliente",
     *     tags={"client"},
     *     description="Crear cliente",
     *     operationId="createClient",
     *
     *     @OA\Response(response="200", description="Cliente creado exitosamente"),
     *     @OA\Response(response="422", description="Datos inválidos"),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="nombre",
     *                     description="Nombre del cliente",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="apellido",
     *                     description="Apellido del cliente",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="fecha_nacimiento",
     *                     description="Fecha de nacimiento del cliente",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     *
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $validator = validator($request->post(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date'
        ]);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        $validatedData = $validator->validated();

        $uuid4 = Uuid::uuid4();
        $userId = $uuid4->toString();

        /** @var DynamoDbClient $dynamoDbClient */
        $dynamoDbClient = app(DynamoDbClient::class);

        $result = $dynamoDbClient->putItem([
            'Item' => [
                'id' => [
                    'S' => $userId,
                ],
                'type' => [
                    'S' => 'client',
                ],
                'nombre' => [
                    'S' => $validatedData['nombre'],
                ],
                'apellido' => [
                    'S' => $validatedData['apellido'],
                ],
                'fecha_nacimiento' => [
                    'S' => $validatedData['fecha_nacimiento'],
                ],
                'age' => [
                    'N' => Carbon::parse($validatedData['fecha_nacimiento'])->age
                ],
                'updated_at' => [
                    'N' => (string) time(),
                ],
                'created_at' => [
                    'N' => (string) time(),
                ],
            ],
            'TableName' => 'in_digital_challenge',
        ]);

        return $result->toArray();
    }


    /**
     * @OA\Get(
     *     path="/listclientes",
     *     tags={"client"},
     *     description="Listado de clientes",
     *     operationId="listClients",
     *
     *     @OA\Response(response="200", description="Listado de clientes paginado")
     * )
     *
     *
     *
     * @param DynamoDbClient $dynamoDbClient
     * @return array
     */
    public function list(DynamoDbClient $dynamoDbClient)
    {
        $scanResult = $dynamoDbClient->scan([
            'TableName' => 'in_digital_challenge',
        ]);

        // Este valor fue obtenido de este PORTAL OFICIAL https://databank.worldbank.org/reports.aspx?source=2&series=SP.DYN.LE00.IN&country=
        $lifeExpectancy = '75.3'; // Latin America 2017

        $items = $scanResult->get('Items');

        $clients = array_filter($items, function ($item) {
            return Arr::get($item, 'type.S') === 'client';
        });


        return [
            'data' => array_map(function ($client) use ($lifeExpectancy) {

                $birthDate = Carbon::parse(Arr::get($client, 'fecha_nacimiento.S'));

                $age = $birthDate->age;

                $deathYears = $lifeExpectancy - $age;

                $client['fecha_muerte_probable'] = Carbon::now()->addYears($deathYears)->format('Y-m-d');

                $client['updated_at'] = Arr::get($client, 'updated_at.N');
                $client['created_at'] = Arr::get($client, 'created_at.N');
                $client['id'] = Arr::get($client, 'id.S');
                $client['nombre'] = Arr::get($client, 'nombre.S');
                $client['apellido'] = Arr::get($client, 'apellido.S');
                $client['type'] = Arr::get($client, 'type.S');
                $client['age'] = Arr::get($client, 'age.N');
                $client['fecha_nacimiento'] = Arr::get($client, 'fecha_nacimiento.S');

                return $client;
            }, $clients),
            'LastEvaluatedKey' => $scanResult->get('LastEvaluatedKey'),
        ];
    }
}
