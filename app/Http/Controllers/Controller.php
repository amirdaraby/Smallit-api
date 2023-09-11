<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      title="Smallit Api",
 *      version="1",
 *      description="Smallit Api Swagger Documentation",
 *      @OA\Contact(
 *          email="amir2002.d@gmail.com",
 *      )
 *  ),
 * ),
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * in="header",
 * name="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * ),
 *
 * @OA\Response(response="Unauthorized",
 * description="Authorization Token is invalid",
 *     @OA\JsonContent(
 *      @OA\Property(property="status", type="string", example="error"),
 *      @OA\Property(property="data", example="null"),
 *      @OA\Property(property="message", type="string", example="Unathorized")
 *      ),
 * ),
 *
 * @OA\Response(response="Forbidden", description="User is not resource's owner",
 * @OA\JsonContent(
 *   @OA\Property(property="message", type="string", example="This action is unauthorized.")
 * ),
 * ),
 * @OA\Response(response="NotFound", description="Not found",
 * @OA\JsonContent(
 *     @OA\Property(property="status", type="string", example="success"),
 *     @OA\Property(property="data", example=null),
 *     @OA\Property(property="message", type="string", example="Not found"),
 * ),
 * ),
 *
 *
 */
class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
