<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Book API",
 *         version="1.0.0",
 *         description="API Documentation"
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000",
 *         description="Default server"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT"
 *         )
 *     )
 * )
 */
class OpenApi {}
