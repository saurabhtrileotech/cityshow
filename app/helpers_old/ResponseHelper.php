<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseHelper
{
    /**
     * Prepare success response
     *
     * @param string $apiStatus
     * @param string $apiMessage
     * @param array $apiData
     * @return Illuminate\Http\JsonResponse
     */
    public function success(
        int $apiCode = 200,
        bool $apiStatus = false,
        string $apiMessage = '',
        array $apiData = []
    ): JsonResponse {
        $response['code'] = $apiCode;
        $response['status'] = $apiStatus;
        $response['data'] = $apiData;

        if ($apiMessage) {
            $response['message'] = $apiMessage;
        }

        return response()->json($response);
    }

    /**
     * Prepare custom success response
     *
     * @param string $apiStatus
     * @param string $apiMessage
     * @param array $apiData
     * @return Illuminate\Http\JsonResponse
     */
    public function success_object(
        int $apiCode = 200,
        bool $apiStatus = false,
        string $apiMessage = '',
        $apiData = []
    ): JsonResponse {
        $response['code'] = $apiCode;
        $response['status'] = $apiStatus;
        $response['data'] = $apiData;

        if ($apiMessage) {
            $response['message'] = $apiMessage;
        }

        return response()->json($response);
    }
    
    /**
     * Prepare success response
     *
     * @param string $apiStatus
     * @param string $apiMessage
     * @param Illuminate\Pagination\LengthAwarePaginator $apiData
     * @return Illuminate\Http\JsonResponse
     */
    public function successWithPagination(
        int $statusCode = 200,
        bool $apiStatus,
        string $apiMessage = '',
        LengthAwarePaginator $apiData = null
    ): JsonResponse {
        $response['code'] = $statusCode;
        $response['status'] = $apiStatus;

        // Check response data have pagination or not? Pagination response parameter sets
        if ($apiData->count()) {
            $apiData->appends(['perPage' => $apiData->perPage()]);

            if ($apiData->currentPage() !== $apiData->lastPage()) {
                $nextPage = $apiData->currentPage() + 1;
            } else {
                $nextPage = '';
            }

            $response['data'] = $apiData->toArray()['data'];
            $response['pagination'] = [
                "total" => $apiData->total(),
                "per_page" => $apiData->perPage(),
                "next_page_number" => $nextPage,
            ];
        } else {
            $response['data'] = [];
            $response['pagination'] = [
                "total" => $apiData->total(),
                "per_page" => $apiData->perPage(),
                "next_page_number" => '',
            ];
        }

        if ($apiMessage) {
            $response['message'] = $apiMessage;
        }

        return response()->json($response);
    }

    /**
     * Prepare error response
     *
     * @param string $statusCode
     * @param string $statusType
     * @param string $customErrorMessage
     * @param object/array $data
     * @return Illuminate\Http\JsonResponse
     */
    public function error(
        string $statusCode = '',
        bool $statusType = null,
        string $message = '',
        $apiData = [],
        string $customErrorCode = ''
    ): JsonResponse {
        $response['code'] = $statusCode;
        $response['status'] = $statusType;
        // $response['custom_error_code'] = $customErrorCode;
        $response['message'] = $message;
        //$response['data'] = $apiData;

        $data = $response;

        return response()->json($data, $statusCode, [], JSON_NUMERIC_CHECK);
    }
}
