<?php

namespace App\Http\Controllers;

use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Server;
use OpenApi\Annotations\Get;

/**
 *
 * @Info(
 *     version="1.0.0",
 *     title="In digital",
 *     description= "This is a microservice, which provide many services as part of InDigital Challenge.",
 *     @Contact(
 *         email="ghuachins@gmail.com",
 *         name="Gian Huachin"
 *     )
 * )
 *
 * @Server(
 *     url="http://in-digital-challenge.local.vh:8108",
 *     description= "development environment"
 * )
 *
 * @Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     description= "Response entity, response result uses this structure uniformly",
 *     @Property(
 *         property="code",
 *         type="string",
 *         description= "response code"
 *     ),
 *     @Property (property = "message", type = "string", description = "response result prompt")
 * )
 *
 * @OA\Get(
 *     path="/",
 *     description="Home page",
 *     operationId="createUser",
 *     @OA\Response(response="200", description="Welcome page")
 * )
 *
 * @package App\Http\Controllers
 */
class SwaggerController
{

}
