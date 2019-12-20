<?php

namespace App\Http\Controllers;

use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Server;

/**
 *
 * @Info(
 *     version="1.0.0",
 *     title="In digital",
 *     description= "This is a demo service, which provides the function of demonstrating the swagger api",
 *     @Contact(
 *         email="mylxsw@aicode.cc",
 *         name="mylxsw"
 *     )
 * )
 *
 * @Server(
 *     url="http://localhost",
 * description= "development environment"
 * )
 *
 * @Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     description= "Response entity, response result uses this structure uniformly",
 *     @Property(
 *         property="code",
 *         type="string",
 * description= "response code"
 *     ),
 * @Property (property = "message", type = "string", description = "response result prompt")
 * )
 *
 * @OA\Get(
 *     path="/",
 *     description="Home page",
 *     @OA\Response(response="default", description="Welcome page")
 * )
 *
 * @package App\Http\Controllers
 */
class SwaggerController
{

}
